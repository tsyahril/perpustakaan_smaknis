<?php

namespace App\Http\Controllers\Guru;

use App\Enums\StatusPinjaman;
use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\DendaSetting;
use App\Models\Transaksi;
use App\Services\RekomendasiBuku;
use App\Support\GrupPeminjaman;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Halaman Akun Saya untuk guru biasa dan siswa: kartu anggota,
 * ringkasan pinjaman, dan pengingat jatuh tempo.
 */
class DashboardController extends Controller
{
    public function index(Request $request, RekomendasiBuku $rekomendasi): View
    {
        $user = $request->user();
        $anggota = $user->anggota;
        $setting = DendaSetting::current();

        $grups = $anggota
            ? GrupPeminjaman::kelompokkan(
                Transaksi::with('buku.kategori')->where('anggota_id', $anggota->id)->get()
            )
            : collect();

        $jumlahGrup = fn (StatusPinjaman $status) => $grups->filter(fn (GrupPeminjaman $g) => $g->status() === $status)->count();

        $jatuhTempo = $grups
            ->flatMap(fn (GrupPeminjaman $g) => $g->aktif())
            ->sortBy(fn (Transaksi $t) => $t->tanggal_kembali_rencana?->getTimestamp() ?? PHP_INT_MAX)
            ->take(5)
            ->values();

        return view('pages.guru.dashboard', [
            'user' => $user,
            'anggota' => $anggota,
            'ringkasan' => [
                'dipinjam' => $grups->sum(fn (GrupPeminjaman $g) => $g->aktif()->count()),
                'menunggu' => $jumlahGrup(StatusPinjaman::Menunggu),
                'terlambat' => $jumlahGrup(StatusPinjaman::Terlambat),
                'denda' => $grups->sum(fn (GrupPeminjaman $g) => $g->dendaBelumLunas($setting)),
            ],
            'jatuhTempo' => $jatuhTempo,
            'terbaru' => $grups->take(3),
            'rekomendasi' => $rekomendasi->untuk($user, 6),
            'totalBuku' => Buku::count(),
        ]);
    }
}
