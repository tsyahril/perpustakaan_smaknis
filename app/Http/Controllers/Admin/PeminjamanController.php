<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $peminjamans = Transaksi::query()
            ->with(['anggota', 'buku'])
            ->where('status', 'pinjam')
            ->when($search, fn ($q) => $q->where(fn ($w) => $w
                ->whereHas('anggota', fn ($a) => $a->where('nama', 'like', "%{$search}%"))
                ->orWhereHas('buku', fn ($b) => $b->where('judul', 'like', "%{$search}%"))))
            ->orderBy('tanggal_pinjam', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.peminjaman.index', compact('peminjamans', 'search'));
    }

    public function create(): View
    {
        $anggotas = Anggota::where('status', 'aktif')->orderBy('nama')->get();
        $bukus = Buku::where('stok', '>', 0)->orderBy('judul')->get();

        return view('pages.admin.peminjaman.create', compact('anggotas', 'bukus'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'anggota_id' => ['required', 'exists:anggotas,id'],
            'buku_id' => ['required', 'exists:bukus,id'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali_rencana' => ['required', 'date', 'after:tanggal_pinjam'],
        ]);

        // Cek apakah buku tersedia
        $buku = Buku::find($validated['buku_id']);
        if ($buku->stok <= 0) {
            return back()->with('error', 'Buku tidak tersedia.');
        }

        // Cek apakah anggota sudah meminjam buku yang sama (belum dikembalikan)
        $sudahPinjam = Transaksi::where('anggota_id', $validated['anggota_id'])
            ->where('buku_id', $validated['buku_id'])
            ->where('status', 'pinjam')
            ->exists();

        if ($sudahPinjam) {
            return back()->with('error', 'Anggota sudah meminjam buku ini. Kembalikan terlebih dahulu.');
        }

        // Buat transaksi
        Transaksi::create($validated);

        // Kurangi stok buku
        $buku->decrement('stok');

        return redirect()->route('admin.peminjaman.index')->with('success', 'Peminjaman berhasil ditambahkan.');
    }

    public function edit(Transaksi $peminjaman): View
    {
        if ($peminjaman->status !== 'pinjam') {
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
            'tanggal_kembali_rencana' => ['required', 'date', 'after:tanggal_pinjam'],
        ]);

        $peminjaman->update($validated);

        return redirect()->route('admin.peminjaman.index')->with('success', 'Peminjaman berhasil diperbarui.');
    }

    public function destroy(Transaksi $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== 'pinjam') {
            return back()->with('error', 'Hanya bisa hapus peminjaman aktif.');
        }

        // Kembalikan stok
        $peminjaman->buku->increment('stok');

        $peminjaman->delete();

        return redirect()->route('admin.peminjaman.index')->with('success', 'Peminjaman berhasil dihapus.');
    }
}
