<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengembalianController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $pengembalians = Transaksi::query()
            ->with(['anggota', 'buku'])
            ->where('status', 'pinjam')
            ->when($search, fn ($q) => $q->where(fn ($w) => $w
                ->whereHas('anggota', fn ($a) => $a->where('nama', 'like', "%{$search}%"))
                ->orWhereHas('buku', fn ($b) => $b->where('judul', 'like', "%{$search}%"))))
            ->orderBy('tanggal_pinjam', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.pengembalian.index', compact('pengembalians', 'search'));
    }

    public function edit(Transaksi $pengembalian): View
    {
        if ($pengembalian->status !== 'pinjam') {
            abort(403, 'Buku sudah dikembalikan.');
        }

        return view('pages.admin.pengembalian.edit', compact('pengembalian'));
    }

    public function update(Request $request, Transaksi $pengembalian): RedirectResponse
    {
        if ($pengembalian->status !== 'pinjam') {
            return back()->with('error', 'Buku sudah dikembalikan.');
        }

        $validated = $request->validate([
            'tanggal_kembali_aktual' => ['required', 'date'],
        ]);

        // Hitung denda dengan perbandingan yang benar
        $tanggalAktual = \Carbon\Carbon::parse($validated['tanggal_kembali_aktual']);
        $tanggalRencana = $pengembalian->tanggal_kembali_rencana;
        
        $denda = 0;
        if ($tanggalAktual->gt($tanggalRencana)) {
            $hariTerlambat = $tanggalAktual->diffInDays($tanggalRencana);
            $denda = $hariTerlambat * 5000;
        }

        // Update transaksi
        $pengembalian->update([
            'tanggal_kembali_aktual' => $validated['tanggal_kembali_aktual'],
            'denda' => $denda,
            'status' => 'dikembalikan',
        ]);

        // Kembalikan stok buku
        $pengembalian->buku->increment('stok');

        return redirect()->route('admin.pengembalian.index')->with('success', 'Pengembalian berhasil diproses.');
    }
}
