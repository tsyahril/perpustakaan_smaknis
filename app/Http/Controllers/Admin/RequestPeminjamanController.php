<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RequestPeminjamanController extends Controller
{
    /**
     * Ambil semua transaksi satu peminjaman dari kunci grup.
     */
    protected function resolveGroup(string $groupKey)
    {
        $query = Transaksi::query()->with(['anggota.kelas', 'buku']);

        if (str_starts_with($groupKey, 'K:')) {
            return $query->where('kode_peminjaman', substr($groupKey, 2))->get();
        }

        return $query->whereKey((int) substr($groupKey, 2))->get();
    }

    /**
     * Daftar pengajuan peminjaman (request): diajukan / disetujui / ditolak.
     * Terpisah dari folder peminjaman yang hanya berisi pinjaman aktif.
     *
     * Dikelompokkan per peminjaman (satu kode_peminjaman) dan difilter
     * lewat dropdown nama peminjam.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'diajukan'); // diajukan | disetujui | ditolak | semua
        $namaPeminjam = $request->query('nama_peminjam', '');

        $rows = Transaksi::query()
            ->with(['anggota.kelas', 'buku'])
            ->when($status !== 'semua' && in_array($status, ['diajukan', 'disetujui', 'ditolak']),
                fn ($q) => $q->where('status_persetujuan', $status))
            ->when($namaPeminjam !== '', fn ($q) => $q->whereHas('anggota',
                fn ($a) => $a->where('nama', 'like', "%{$namaPeminjam}%")))
            ->orderByRaw("CASE WHEN status_persetujuan = 'diajukan' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get();

        // Satu kode_peminjaman = satu kali pinjam (bisa lebih dari satu buku).
        $groups = $rows
            ->groupBy(fn ($t) => $t->getGroupKeyAttribute())
            ->map(function ($items) {
                $items = $items->sortBy('tanggal_pinjam')->values();
                $first = $items->first();

                return (object) [
                    'key' => $first->getGroupKeyAttribute(),
                    'kode' => $first->kode_peminjaman,
                    'anggota' => $first->anggota,
                    'items' => $items,
                    'jumlah_buku' => $items->count(),
                    'tanggal_pinjam' => $items->min('tanggal_pinjam'),
                    'tanggal_kembali_rencana' => $items->pluck('tanggal_kembali_rencana')->filter()->min(),
                    'status_persetujuan' => $first->status_persetujuan,
                    'catatan' => $first->catatan,
                ];
            })
            ->sortByDesc('tanggal_pinjam')
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

        // Dropdown peminjam: nama anggota yang punya pengajuan.
        $daftarPeminjam = Anggota::query()
            ->whereHas('transaksis')
            ->orderBy('nama')
            ->pluck('nama');

        // Jumlah pengajuan per status (dihitung per peminjaman, bukan per buku).
        $counts = [];
        foreach (['diajukan', 'disetujui', 'ditolak'] as $s) {
            $counts[$s] = Transaksi::where('status_persetujuan', $s)
                ->get()
                ->groupBy(fn ($t) => $t->getGroupKeyAttribute())
                ->count();
        }

        return view('pages.admin.request-peminjaman.index', compact(
            'requests', 'status', 'namaPeminjam', 'daftarPeminjam', 'counts'
        ));
    }

    public function approve(string $groupKey): RedirectResponse
    {
        $items = $this->resolveGroup($groupKey);

        abort_if($items->isEmpty(), 404);

        if ($items->first()->status_persetujuan !== 'diajukan') {
            return back()->with('error', 'Hanya pengajuan berstatus diajukan yang bisa disetujui.');
        }

        $items->each->update(['status_persetujuan' => 'disetujui']);

        return back()->with('success', 'Pengajuan disetujui (' . $items->count() . ' buku), pindah ke Data Peminjaman.');
    }

    public function reject(Request $request, string $groupKey): RedirectResponse
    {
        $items = $this->resolveGroup($groupKey);

        abort_if($items->isEmpty(), 404);

        if ($items->first()->status_persetujuan !== 'diajukan') {
            return back()->with('error', 'Hanya pengajuan berstatus diajukan yang bisa ditolak.');
        }

        $request->validate(['catatan' => ['nullable', 'string', 'max:500']]);

        DB::transaction(function () use ($items, $request) {
            foreach ($items as $item) {
                $item->update([
                    'status_persetujuan' => 'ditolak',
                    'catatan' => $request->input('catatan'),
                ]);

                // Stok ditahan sejak pengajuan dibuat, jadi dikembalikan bila ditolak.
                $item->buku->increment('stok');
            }
        });

        return back()->with('success', 'Pengajuan ditolak, stok dikembalikan.');
    }
}