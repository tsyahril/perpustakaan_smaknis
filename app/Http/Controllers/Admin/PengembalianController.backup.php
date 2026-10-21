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

    public function index(Request $request): View
    {
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

        return view('pages.admin.pengembalian.index', compact(
            'pengembalians', 'namaPeminjam', 'namaBuku', 'kondisi', 'daftarPeminjam', 'daftarBuku'
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
        $items = $this->resolveGroup($groupKey)->where('status', 'pinjam');

        if ($items->isEmpty()) {
            return back()->with('error', 'Tidak ada buku aktif pada peminjaman ini.');
        }

        $validated = $request->validate([
            'tanggal_kembali_aktual' => ['required', 'array'],
            'tanggal_kembali_aktual.*' => ['nullable', 'date'],
            'kondisi' => ['required', 'array'],
            'kondisi.*' => ['required', 'in:baik,rusak,hilang'],
            'catatan' => ['nullable', 'array'],
            'catatan.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $totalDenda = 0;
        $jumlahBuku = 0;

        DB::transaction(function () use ($items, $validated, &$totalDenda, &$jumlahBuku) {
            foreach ($items as $item) {
                $kondisi = $validated['kondisi'][$item->id] ?? 'baik';
                $catatan = $validated['catatan'][$item->id] ?? null;
                // Tiap buku boleh punya tanggal kembali sendiri.
                $tglAktual = $validated['tanggal_kembali_aktual'][$item->id] ?? null;
                $tglAktual = ! empty($tglAktual) ? Carbon::parse($tglAktual) : Carbon::today();
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

                // Stok kembali hanya bila buku fisik kembali (baik/rusak).
                if ($kondisi !== 'hilang') {
                    $item->buku->increment('stok');
                }

                $totalDenda += $rincian['total'];
                $jumlahBuku++;
            }
        });

        $pesan = "Pengembalian {$jumlahBuku} buku berhasil diproses.";
        if ($totalDenda > 0) {
            $pesan .= ' Total denda Rp' . number_format($totalDenda, 0, ',', '.') . '.';
        }

        return redirect()->route('admin.pengembalian.index')->with('success', $pesan);
    }

    /**
     * Hapus satu riwayat peminjaman (bila tidak ada denda belum lunas).
     */
    public function destroy(string $groupKey): RedirectResponse
    {
        $items = $this->resolveGroup($groupKey);

        if ($items->where('status', 'dikembalikan')->isEmpty()) {
            return back()->with('error', 'Riwayat tidak ditemukan.');
        }

        $belumLunas = $items->filter(fn ($i) => $i->denda > 0 && ! $i->isDendaLunas());

        if ($belumLunas->isNotEmpty()) {
            $total = $belumLunas->sum('denda');

            return back()->with('error', 'Tidak bisa dihapus: masih ada denda belum dibayar sebesar Rp' . number_format($total, 0, ',', '.') . '.');
        }

        $items->each->delete();

        return back()->with('success', 'Riwayat pengembalian berhasil dihapus.');
    }

    /**
     * Hapus banyak riwayat sekaligus (kecuali yang denda belum lunas).
     */
    public function destroySelected(Request $request): RedirectResponse
    {
        $request->validate([
            'group_keys' => ['required', 'array', 'min:1'],
            'group_keys.*' => ['required', 'string'],
        ]);

        $dihapus = 0;
        $dilewati = [];

        foreach ($request->input('group_keys', []) as $key) {
            $items = $this->resolveGroup($key);

            if ($items->where('status', 'dikembalikan')->isEmpty()) {
                continue;
            }

            $belumLunas = $items->filter(fn ($i) => $i->denda > 0 && ! $i->isDendaLunas());

            if ($belumLunas->isNotEmpty()) {
                $nama = $items->first()->anggota->nama ?? '-';
                $dilewati[] = $nama . ' (Rp' . number_format($belumLunas->sum('denda'), 0, ',', '.') . ')';
                continue;
            }

            $items->each->delete();
            $dihapus++;
        }

        $pesan = "{$dihapus} riwayat berhasil dihapus.";
        if (! empty($dilewati)) {
            $pesan .= ' Dilewati karena denda belum dibayar: ' . implode(', ', $dilewati) . '.';
        }

        return back()->with('success', $pesan);
    }
}
