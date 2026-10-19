<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class RequestPengembalianController extends Controller
{
    /**
     * Daftar request pengembalian: pinjaman aktif (status pinjam) yang
     * menunggu verifikasi pustakawan. Dikelompokkan per peminjaman
     * (satu kode_peminjaman) sehingga semua buku bisa diverifikasi sekaligus.
     */
    public function index(Request $request): View
    {
        $namaPeminjam = $request->query('nama_peminjam', '');
        $namaBuku = $request->query('nama_buku', '');

        $rows = Transaksi::query()
            ->with(['anggota.kelas', 'buku'])
            ->where('status', 'pinjam')
            ->where('status_persetujuan', '!=', 'ditolak')
            ->when($namaPeminjam !== '', fn ($q) => $q->whereHas('anggota',
                fn ($a) => $a->where('nama', 'like', "%{$namaPeminjam}%")))
            ->when($namaBuku !== '', fn ($q) => $q->whereHas('buku',
                fn ($b) => $b->where('judul', 'like', "%{$namaBuku}%")))
            ->orderByRaw('tanggal_kembali_rencana IS NULL')
            ->orderBy('tanggal_kembali_rencana', 'asc')
            ->get();

        // Satu kode_peminjaman = satu kali pinjam (bisa lebih dari satu buku).
        $groups = $rows
            ->groupBy(fn ($t) => $t->getGroupKeyAttribute())
            ->map(function ($items) {
                $items = $items->sortBy('tanggal_kembali_rencana')->values();

                return (object) [
                    'key' => $items->first()->getGroupKeyAttribute(),
                    'kode' => $items->first()->kode_peminjaman,
                    'anggota' => $items->first()->anggota,
                    'items' => $items,
                    'jumlah_buku' => $items->count(),
                    'tanggal_pinjam' => $items->min('tanggal_pinjam'),
                    'tanggal_kembali_rencana' => $items->pluck('tanggal_kembali_rencana')->filter()->min(),
                    'terlambat' => (int) $items->max(fn ($i) => $i->hitungHariTerlambat()),
                ];
            })
            ->sortBy(fn ($g) => $g->tanggal_kembali_rencana?->getTimestamp() ?? PHP_INT_MAX)
            ->values();

        $perPage = 10;
        $page = max(1, (int) $request->query('page', 1));
        $requests = new LengthAwarePaginator(
            $groups->forPage($page, $perPage)->values(),
            $groups->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $daftarPeminjam = Anggota::where('status', 'aktif')->orderBy('nama')->get(['id', 'nama', 'nomor_anggota']);
        $daftarBuku = Buku::orderBy('judul')->get(['id', 'judul', 'stok']);

        $telatCount = Transaksi::where('status', 'pinjam')
            ->where('status_persetujuan', '!=', 'ditolak')
            ->whereDate('tanggal_kembali_rencana', '<', Carbon::today())
            ->count();

        return view('pages.admin.request-pengembalian.index', compact(
            'requests', 'namaPeminjam', 'namaBuku', 'daftarPeminjam', 'daftarBuku', 'telatCount'
        ));
    }
}