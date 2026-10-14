<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DendaController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $dendas = Transaksi::query()
            ->with(['anggota', 'buku'])
            ->where('status', 'dikembalikan')
            ->where('denda', '>', 0)
            ->when($search, fn ($q) => $q->where(fn ($w) => $w
                ->whereHas('anggota', fn ($a) => $a->where('nama', 'like', "%{$search}%"))
                ->orWhereHas('buku', fn ($b) => $b->where('judul', 'like', "%{$search}%"))))
            ->orderBy('tanggal_kembali_aktual', 'desc')
            ->paginate(10)
            ->withQueryString();

        $totalDenda = $dendas->sum('denda');

        return view('pages.admin.denda.index', compact('dendas', 'search', 'totalDenda'));
    }

    public function show(Transaksi $denda): View
    {
        if ($denda->status !== 'dikembalikan' || $denda->denda <= 0) {
            abort(404);
        }

        return view('pages.admin.denda.show', compact('denda'));
    }
}
