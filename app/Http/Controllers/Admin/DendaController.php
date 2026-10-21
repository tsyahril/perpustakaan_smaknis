<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\DendaSetting;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DendaController extends Controller
{
    /**
     * Halaman Denda kini memuat dua tab:
     *  - tab=setting : pengaturan tarif (dulu halaman "Setting Denda", khusus admin)
     *  - tab=tagihan : data denda & tagihan anggota (default)
     */
    public function index(Request $request): View|RedirectResponse
    {
        $tab = $request->query('tab') === 'setting' ? 'setting' : 'tagihan';

        if ($tab === 'setting') {
            if (! auth()->user()?->isAdmin()) {
                return redirect()->route('admin.denda.index');
            }

            return view('pages.admin.denda.index', [
                'tab' => 'setting',
                'setting' => DendaSetting::current(),
            ]);
        }

        $search = $request->query('search');
        $jenis = $request->query('jenis', ''); // terlambat | rusak | hilang

        $dendas = Transaksi::query()
            ->with(['anggota.kelas', 'buku'])
            ->where('status', 'dikembalikan')
            ->where('denda', '>', 0)
            ->when($search, fn ($q) => $q->where(fn ($w) => $w
                ->whereHas('anggota', fn ($a) => $a->where('nama', 'like', "%{$search}%"))
                ->orWhereHas('buku', fn ($b) => $b->where('judul', 'like', "%{$search}%"))))
            ->when($jenis === 'terlambat', fn ($q) => $q->where('denda_terlambat', '>', 0))
            ->when($jenis === 'rusak', fn ($q) => $q->where('kondisi', 'rusak'))
            ->when($jenis === 'hilang', fn ($q) => $q->where('kondisi', 'hilang'))
            ->orderBy('tanggal_kembali_aktual', 'desc')
            ->paginate(10)
            ->withQueryString();

        $totalDenda = (clone $dendas->getCollection())->sum('denda');
        // Total keseluruhan (tanpa paginasi) untuk kartu ringkasan.
        $grandTotal = Transaksi::where('status', 'dikembalikan')->where('denda', '>', 0)->sum('denda');

        // Tagihan per anggota: riwayat denda apa saja yang dia dapat.
        $tagihan = Transaksi::query()
            ->selectRaw('anggota_id, COUNT(*) as jumlah, SUM(denda) as total, SUM(denda_terlambat) as total_terlambat, SUM(denda_kondisi) as total_kondisi, SUM(CASE WHEN denda_dibayar = 0 THEN denda ELSE 0 END) as total_belum_dibayar')
            ->where('status', 'dikembalikan')
            ->where('denda', '>', 0)
            ->groupBy('anggota_id')
            ->with('anggota')
            ->orderByDesc('total')
            ->limit(20)
            ->get();

        return view('pages.admin.denda.index', compact('tab', 'dendas', 'search', 'jenis', 'totalDenda', 'grandTotal', 'tagihan'));
    }

    public function show(Transaksi $denda): View
    {
        if ($denda->status !== 'dikembalikan' || $denda->denda <= 0) {
            abort(404);
        }

        $denda->load(['anggota.kelas', 'buku']);

        return view('pages.admin.denda.show', compact('denda'));
    }

    /**
     * Tandai denda sebagai sudah dibayar (lunas).
     * Setelah lunas, riwayat pengembalian terkait boleh dihapus.
     */
    public function bayar(Transaksi $denda): RedirectResponse
    {
        if ($denda->status !== 'dikembalikan' || $denda->denda <= 0) {
            abort(404);
        }

        $denda->update([
            'denda_dibayar' => true,
            'denda_dibayar_pada' => now(),
        ]);

        return back()->with('success', 'Denda Rp' . number_format($denda->denda, 0, ',', '.') . ' ditandai lunas.');
    }

    /**
     * Riwayat tagihan satu anggota: denda apa saja yang dia dapat.
     */
    public function tagihanAnggota(Anggota $anggota): View
    {
        $riwayat = Transaksi::with('buku')
            ->where('anggota_id', $anggota->id)
            ->where('status', 'dikembalikan')
            ->where('denda', '>', 0)
            ->orderBy('tanggal_kembali_aktual', 'desc')
            ->paginate(10);

        $total = Transaksi::where('anggota_id', $anggota->id)
            ->where('status', 'dikembalikan')->sum('denda');

        $totalBelumDibayar = Transaksi::where('anggota_id', $anggota->id)
            ->where('status', 'dikembalikan')
            ->where('denda', '>', 0)
            ->where('denda_dibayar', false)
            ->sum('denda');

        return view('pages.admin.denda.tagihan', compact('anggota', 'riwayat', 'total', 'totalBelumDibayar'));
    }
}