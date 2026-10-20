<?php

namespace App\Http\Controllers\Pustakawan;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $groupCount = fn () => DB::table('transaksis')
            ->selectRaw("COUNT(DISTINCT COALESCE(NULLIF(kode_peminjaman, ''), CONCAT('T:', id))) AS total");

        $totalBuku = Buku::count();

        $pengajuanPeminjaman = (int) $groupCount()
            ->where('status_persetujuan', 'diajukan')
            ->value('total');

        $peminjamanAktif = (int) $groupCount()
            ->where('status_persetujuan', 'disetujui')
            ->where('status', 'pinjam')
            ->value('total');

        $riwayatPengembalian = (int) $groupCount()
            ->where('status', 'dikembalikan')
            ->value('total');

        $dendaBelumLunas = DB::table('transaksis')
            ->where('denda', '>', 0)
            ->where('denda_dibayar', false)
            ->where('status', 'dikembalikan')
            ->count();

        return view('pages.pustakawan.dashboard', [
            'user' => auth()->user(),
            'totalBuku' => $totalBuku,
            'pengajuanPeminjaman' => $pengajuanPeminjaman,
            'peminjamanAktif' => $peminjamanAktif,
            'riwayatPengembalian' => $riwayatPengembalian,
            'dendaBelumLunas' => $dendaBelumLunas,
        ]);
    }
}