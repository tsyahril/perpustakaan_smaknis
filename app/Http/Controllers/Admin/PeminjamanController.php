<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kelas;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    /**
     * Halaman Peminjaman kini memuat dua tab sekaligus:
     *  - tab=pengajuan : hasil penggabungan menu "Request Peminjaman"
     *  - tab=data      : data peminjaman yang sudah disetujui
     */
    public function index(Request $request): View
    {
        $tab = $request->query('tab') === 'pengajuan' ? 'pengajuan' : 'data';

        if ($tab === 'pengajuan') {
            return $this->tabPengajuan($request);
        }

        $search = $request->query('search');
        $status = $request->query('status', '');

        // Folder peminjaman hanya berisi pinjaman yang sudah disetujui.
        // Pengajuan (diajukan/ditolak) tampil pada tab "Pengajuan".
        $peminjamans = Transaksi::query()
            ->with(['anggota.kelas', 'buku'])
            ->where('status_persetujuan', 'disetujui')
            ->when($status !== '' && in_array($status, ['pinjam', 'dikembalikan']),
                fn ($q) => $q->where('status', $status))
            ->when($search, fn ($q) => $q->where(fn ($w) => $w
                ->whereHas('anggota', fn ($a) => $a->where('nama', 'like', "%{$search}%"))
                ->orWhereHas('buku', fn ($b) => $b->where('judul', 'like', "%{$search}%"))))
            ->orderBy('tanggal_pinjam', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.peminjaman.index', compact('tab', 'peminjamans', 'search', 'status'));
    }

    /**
     * Isi tab "Pengajuan": pengajuan pinjam menunggu persetujuan.
     * Dikelompokkan per peminjaman (satu kode_peminjaman).
     */
    protected function tabPengajuan(Request $request): View
    {
        $tab = 'pengajuan';
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

        return view('pages.admin.peminjaman.index', compact(
            'tab', 'requests', 'status', 'namaPeminjam', 'daftarPeminjam', 'counts'
        ));
    }

    public function create(): View
    {
        // Data untuk alur bercabang: role -> (kelas bila siswa) -> anggota -> buku + stok.
        $kelas = Kelas::orderBy('nama')->get();
        $anggotas = Anggota::with('kelas')->where('status', 'aktif')->orderBy('nama')->get();
        $bukus = Buku::with('kategori')->orderBy('judul')->get();

        // Siapkan JSON di controller agar Blade tidak perlu parsing
        // arrow-function di dalam @json (menyebabkan ParseError di Laravel 10).
        $anggotasJson = $anggotas->map(function ($a) {
            return [
                'id' => $a->id,
                'nama' => $a->nama,
                'nomor' => $a->nomor_anggota,
                'role' => $a->jenis_anggota,
                'kelas_id' => $a->id_kelas,
                'kelas_nama' => $a->kelas ? $a->kelas->nama : null,
            ];
        })->values()->toJson();

        $bukusJson = $bukus->map(function ($b) {
            return [
                'id' => $b->id,
                'judul' => $b->judul,
                'penulis' => $b->penulis,
                'penerbit' => $b->penerbit,
                'tahun' => $b->tahun_terbit,
                'isbn' => $b->isbn,
                'kategori' => $b->kategori ? $b->kategori->nama : null,
                'stok' => $b->stok,
                'cover' => $b->coverUrl(),
            ];
        })->values()->toJson();

        return view('pages.admin.peminjaman.create', compact('anggotas', 'bukus', 'kelas', 'anggotasJson', 'bukusJson'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'anggota_id' => ['required', 'exists:anggotas,id'],
            'buku_ids' => ['required', 'array', 'min:1', 'max:5'],
            'buku_ids.*' => ['required', 'exists:bukus,id'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali_rencana' => ['nullable', 'date', 'after:tanggal_pinjam'],
            'tanggal_kembali_rencana_per_buku' => ['nullable', 'array'],
            'tanggal_kembali_rencana_per_buku.*' => ['nullable', 'date', 'after:tanggal_pinjam'],
            'ajukan_persetujuan' => ['nullable', 'boolean'],
        ]);

        $anggota = Anggota::findOrFail($validated['anggota_id']);
        if ($anggota->status !== 'aktif') {
            return back()->withInput()->with('error', 'Anggota tidak aktif.');
        }

        $bukuIds = array_unique($validated['buku_ids']);
        $needApproval = (bool) ($validated['ajukan_persetujuan'] ?? true);

        return DB::transaction(function () use ($bukuIds, $validated, $needApproval) {
            $kode = 'PJM-' . date('Ymd-His') . '-' . strtoupper(substr(uniqid(), -4));

            foreach ($bukuIds as $bukuId) {
                $buku = Buku::lockForUpdate()->find($bukuId);

                if ($buku->stok <= 0) {
                    DB::rollBack();
                    return back()->withInput()->with('error', "Stok buku \"{$buku->judul}\" habis, tidak bisa dipinjam.");
                }

                $sudahPinjam = Transaksi::where('anggota_id', $validated['anggota_id'])
                    ->where('buku_id', $bukuId)
                    ->where('status', 'pinjam')
                    ->where('status_persetujuan', '!=', 'ditolak')
                    ->exists();

                if ($sudahPinjam) {
                    DB::rollBack();
                    return back()->withInput()->with('error', "Anggota sudah meminjam \"{$buku->judul}\". Kembalikan terlebih dahulu.");
                }

                Transaksi::create([
                    'anggota_id' => $validated['anggota_id'],
                    'buku_id' => $bukuId,
                    'kode_peminjaman' => $kode,
                    'tanggal_pinjam' => $validated['tanggal_pinjam'],
                    // Tanggal rencana per buku: kalau input per-buku diisi (meskipun
                    // dikosongkan) itu yang dipakai; kosong = tanpa rencana.
                    'tanggal_kembali_rencana' => array_key_exists(
                        $bukuId,
                        $validated['tanggal_kembali_rencana_per_buku'] ?? []
                    )
                        ? ($validated['tanggal_kembali_rencana_per_buku'][$bukuId] ?: null)
                        : ($validated['tanggal_kembali_rencana'] ?? null),
                    'status' => 'pinjam',
                    'status_persetujuan' => $needApproval ? 'diajukan' : 'disetujui',
                ]);

                // Stok dikurangi saat disetujui langsung; saat pengajuan, stok ditahan
                // dengan cara dikurangi juga agar tidak dipinjam orang lain,
                // dan dikembalikan bila pengajuan ditolak.
                $buku->decrement('stok');
            }

            $pesan = $needApproval
                ? 'Pengajuan peminjaman (' . count($bukuIds) . ' buku) berhasil dibuat, menunggu persetujuan pustakawan.'
                : 'Peminjaman (' . count($bukuIds) . ' buku) berhasil ditambahkan.';

            $tujuan = $needApproval
                ? route('admin.peminjaman.index', ['tab' => 'pengajuan'])
                : route('admin.peminjaman.index');

            return redirect()->to($tujuan)->with('success', $pesan);
        });
    }

    public function edit(Transaksi $peminjaman): View
    {
        if ($peminjaman->status !== 'pinjam' || $peminjaman->status_persetujuan === 'ditolak') {
            abort(403, 'Data bukan peminjaman aktif.');
        }

        return view('pages.admin.peminjaman.edit', compact('peminjaman'));
    }

    public function update(Request $request, Transaksi $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== 'pinjam') {
            return back()->with('error', 'Data bukan peminjaman aktif.');
        }

        $validated = $request->validate([
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali_rencana' => ['nullable', 'date', 'after:tanggal_pinjam'],
        ]);

        $peminjaman->update($validated);

        return redirect()->route('admin.peminjaman.index')->with('success', 'Peminjaman berhasil diperbarui.');
    }

    public function destroy(Transaksi $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== 'pinjam') {
            return back()->with('error', 'Hanya bisa hapus peminjaman aktif.');
        }

        // Kembalikan stok yang sempat ditahan/dipinjam.
        $peminjaman->buku->increment('stok');
        $peminjaman->delete();

        return redirect()->route('admin.peminjaman.index')->with('success', 'Peminjaman berhasil dihapus.');
    }
}
