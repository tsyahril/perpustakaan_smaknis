<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\DendaSetting;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengembalianController extends Controller
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
     * Halaman Pengembalian kini memuat dua tab sekaligus:
     *  - tab=pengajuan : pinjaman aktif menunggu verifikasi (dulu "Request Pengembalian")
     *  - tab=riwayat   : riwayat buku yang sudah dikembalikan
     */
    public function index(Request $request): View
    {
        $tab = $request->query('tab') === 'pengajuan' ? 'pengajuan' : 'riwayat';

        if ($tab === 'pengajuan') {
            return $this->tabPengajuan($request);
        }

        $namaPeminjam = $request->query('nama_peminjam', '');
        $namaBuku = $request->query('nama_buku', '');
        $kondisi = $request->query('kondisi', ''); // baik | rusak | hilang

        $rows = Transaksi::query()
            ->with(['anggota.kelas', 'buku'])
            ->where('status', 'dikembalikan')
            ->where('status_persetujuan', '!=', 'ditolak')
            ->when($namaPeminjam !== '', fn ($q) => $q->whereHas('anggota',
                fn ($a) => $a->where('nama', 'like', "%{$namaPeminjam}%")))
            ->when($namaBuku !== '', fn ($q) => $q->whereHas('buku',
                fn ($b) => $b->where('judul', 'like', "%{$namaBuku}%")))
            ->when($kondisi !== '' && in_array($kondisi, ['baik', 'rusak', 'hilang']),
                fn ($q) => $q->where('kondisi', $kondisi))
            ->orderBy('tanggal_kembali_aktual', 'desc')
            ->get();

        // Satu kode_peminjaman = satu kali pinjam (bisa lebih dari satu buku).
        $groups = $rows
            ->groupBy(fn ($t) => $t->getGroupKeyAttribute())
            ->map(function ($items) {
                $items = $items->sortByDesc('tanggal_kembali_aktual')->values();
                $first = $items->first();

                return (object) [
                    'key' => $first->getGroupKeyAttribute(),
                    'kode' => $first->kode_peminjaman,
                    'anggota' => $first->anggota,
                    'items' => $items,
                    'jumlah_buku' => $items->count(),
                    'tanggal_kembali_aktual' => $items->max('tanggal_kembali_aktual'),
                    'total_denda' => (int) $items->sum('denda'),
                    'denda_belum_lunas' => (int) $items->sum(fn ($i) => ($i->denda > 0 && ! $i->isDendaLunas()) ? $i->denda : 0),
                ];
            })
            ->sortByDesc('tanggal_kembali_aktual')
            ->values();

        $perPage = 10;
        $page = max(1, (int) $request->query('page', 1));
        $pengembalians = new LengthAwarePaginator(
            $groups->forPage($page, $perPage)->values(),
            $groups->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $daftarPeminjam = Anggota::where('status', 'aktif')->orderBy('nama')->get(['id', 'nama', 'nomor_anggota']);
        $daftarBuku = Buku::orderBy('judul')->get(['id', 'judul', 'stok']);

        $tab = 'riwayat';

        return view('pages.admin.pengembalian.index', compact(
            'tab', 'pengembalians', 'namaPeminjam', 'namaBuku', 'kondisi', 'daftarPeminjam', 'daftarBuku'
        ));
    }

    /**
     * Isi tab "Pengajuan": pinjaman aktif (status pinjam) yang menunggu
     * verifikasi pustakawan. Dikelompokkan per peminjaman.
     */
    protected function tabPengajuan(Request $request): View
    {
        $tab = 'pengajuan';
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

        return view('pages.admin.pengembalian.index', compact(
            'tab', 'requests', 'namaPeminjam', 'namaBuku', 'daftarPeminjam', 'daftarBuku', 'telatCount'
        ));
    }

    /**
     * Detail satu peminjaman yang sudah dikembalikan.
     */
    public function show(string $groupKey): View
    {
        $items = $this->resolveGroup($groupKey);

        abort_if($items->isEmpty(), 404);

        return view('pages.admin.pengembalian.show', [
            'groupKey' => $groupKey,
            'items' => $items,
        ]);
    }

    public function edit(string $groupKey): View
    {
        $items = $this->resolveGroup($groupKey)->where('status', 'pinjam')->values();

        abort_if($items->isEmpty(), 403, 'Tidak ada buku aktif pada peminjaman ini.');

        $items->loadMissing(['anggota.kelas', 'buku']);
        $setting = DendaSetting::current();

        return view('pages.admin.pengembalian.edit', [
            'groupKey' => $groupKey,
            'items' => $items,
            'setting' => $setting,
            'today' => Carbon::today(),
        ]);
    }

    
public function update(Request $request, string $groupKey): RedirectResponse
{
    $validated = $request->validate([
        'transaksi_ids' => ['required', 'array', 'min:1'],
        'transaksi_ids.*' => ['required', 'integer', 'distinct'],
        'tanggal_kembali_aktual' => ['required', 'array'],
        'tanggal_kembali_aktual.*' => ['nullable', 'date'],
        'kondisi' => ['required', 'array'],
        'kondisi.*' => ['required', 'in:baik,rusak,hilang'],
        'catatan' => ['nullable', 'array'],
        'catatan.*' => ['nullable', 'string', 'max:1000'],
    ]);

    $semuaItemAktif = $this->resolveGroup($groupKey)
        ->where('status', 'pinjam')
        ->values();

    if ($semuaItemAktif->isEmpty()) {
        return back()->with('error', 'Tidak ada buku aktif pada peminjaman ini.');
    }

    $ids = array_map('intval', $validated['transaksi_ids']);

    // Pastikan semua ID pilihan benar-benar termasuk peminjaman ini.
    $items = $semuaItemAktif
        ->whereIn('id', $ids)
        ->values();

    if ($items->count() !== count($ids)) {
        return back()->with('error', 'Pilihan buku tidak valid atau sudah diproses.');
    }

    $totalDenda = 0;
    $jumlahBuku = 0;

    DB::transaction(function () use (
        $items,
        $validated,
        &$totalDenda,
        &$jumlahBuku
    ) {
        foreach ($items as $item) {
            $item->loadMissing('buku');

            $kondisi = $validated['kondisi'][$item->id] ?? 'baik';
            $catatan = $validated['catatan'][$item->id] ?? null;

            $tglAktual = $validated['tanggal_kembali_aktual'][$item->id] ?? null;
            $tglAktual = !empty($tglAktual)
                ? Carbon::parse($tglAktual)
                : Carbon::today();

            $rincian = $item->hitungDenda($kondisi, $tglAktual);

            $item->update([
                'tanggal_kembali_aktual' => $tglAktual->toDateString(),
                'kondisi' => $kondisi,
                'hari_terlambat' => $rincian['hari'],
                'denda_terlambat' => $rincian['denda_terlambat'],
                'denda_kondisi' => $rincian['denda_kondisi'],
                'denda' => $rincian['total'],
                'catatan' => $catatan,
                'status' => 'dikembalikan',
            ]);

            // Stok bertambah hanya jika buku fisiknya kembali.
            if ($kondisi !== 'hilang') {
                $item->buku->increment('stok');
            }

            $totalDenda += $rincian['total'];
            $jumlahBuku++;
        }
    });

    $pesan = "{$jumlahBuku} buku berhasil diproses.";
    if ($totalDenda > 0) {
        $pesan .= ' Total denda Rp' .
            number_format($totalDenda, 0, ',', '.') . '.';
    }

    return redirect()
        ->route('admin.pengembalian.index')
        ->with('success', $pesan);
}


    
/**
 * Hapus satu transaksi buku dari riwayat.
 */
public function destroy(string $groupKey): RedirectResponse
{
    // Parameter route sekarang adalah ID transaksi buku.
    $item = Transaksi::with(['anggota', 'buku'])
        ->find((int) $groupKey);

    if (! $item || $item->status !== 'dikembalikan') {
        return back()->with('error', 'Riwayat buku tidak ditemukan.');
    }

    if (! $item->isDendaLunas()) {
        return back()->with(
            'error',
            'Buku ini tidak bisa dihapus karena dendanya belum lunas.'
        );
    }

    $item->delete();

    return back()->with('success', 'Riwayat buku berhasil dihapus.');
}

/**
 * Hapus beberapa transaksi buku terpilih.
 * Buku dengan denda belum lunas dilewati secara individual.
 */

/**
 * Hapus beberapa transaksi buku terpilih.
 * Buku dengan denda belum lunas dilewati secara individual.
 */
public function destroySelected(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'transaksi_ids' => ['required', 'array', 'min:1'],
        'transaksi_ids.*' => ['required', 'integer', 'distinct'],
    ]);

    // Simpan identitas grup sebelum transaksi dihapus.
    $transaksiAwal = Transaksi::find(
        (int) $validated['transaksi_ids'][0]
    );

    $groupKey = $transaksiAwal?->getGroupKeyAttribute();

    $dihapus = 0;
    $dilewati = 0;

    foreach ($validated['transaksi_ids'] as $id) {
        $item = Transaksi::find((int) $id);

        if (! $item || $item->status !== 'dikembalikan') {
            continue;
        }

        if (! $item->isDendaLunas()) {
            $dilewati++;
            continue;
        }

        $item->delete();
        $dihapus++;
    }

    $pesan = "{$dihapus} riwayat buku berhasil dihapus.";

    if ($dilewati > 0) {
        $pesan .= " {$dilewati} buku dilewati karena dendanya belum lunas.";
    }

    // Kembali ke detail jika masih ada riwayat pengembalian.
    if ($groupKey) {
        $masihAdaRiwayat = $this->resolveGroup($groupKey)
            ->contains(fn ($item) => $item->status === 'dikembalikan');

        if ($masihAdaRiwayat) {
            return redirect()
                ->route('admin.pengembalian.show', ['groupKey' => $groupKey])
                ->with('success', $pesan);
        }
    }

    // Jika seluruh riwayat grup sudah terhapus, kembali ke daftar.
    return redirect()
        ->route('admin.pengembalian.index')
        ->with('success', $pesan);
}


}
