<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $awalPeriode = $now->copy()->startOfMonth()->subMonths(11);
        $akhirPeriode = $now->copy()->endOfMonth();

        // Satu kode peminjaman dihitung sebagai satu transaksi.
        // Data lama tanpa kode dihitung per baris transaksi.
        $groupCount = fn () => DB::table('transaksis')
            ->selectRaw("COUNT(DISTINCT COALESCE(NULLIF(kode_peminjaman, ''), CONCAT('T:', id))) AS total");

        $totalAnggota = DB::table('anggotas')->count();
        $totalBuku = DB::table('bukus')->count();

        $totalPeminjaman = (int) $groupCount()
            ->where('status_persetujuan', 'disetujui')
            ->value('total');

        $peminjamanAktif = (int) $groupCount()
            ->where('status_persetujuan', 'disetujui')
            ->where('status', 'pinjam')
            ->value('total');

        $menungguVerifikasi = (int) $groupCount()
            ->where('status_persetujuan', 'diajukan')
            ->value('total');

        // Ambil jumlah peminjaman yang disetujui untuk setiap bulan.
        $dataBulanan = DB::table('transaksis')
            ->selectRaw("
                DATE_FORMAT(tanggal_pinjam, '%Y-%m') AS periode,
                COUNT(DISTINCT COALESCE(NULLIF(kode_peminjaman, ''), CONCAT('T:', id))) AS total
            ")
            ->where('status_persetujuan', 'disetujui')
            ->whereBetween('tanggal_pinjam', [
                $awalPeriode->toDateString(),
                $akhirPeriode->toDateString(),
            ])
            ->groupBy('periode')
            ->pluck('total', 'periode');

        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar',
            4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep',
            10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $grafikPeminjaman = [];

        for ($i = 0; $i < 12; $i++) {
            $tanggal = $awalPeriode->copy()->addMonths($i);
            $periode = $tanggal->format('Y-m');

            $grafikPeminjaman[] = [
                'periode' => $periode,
                'bulan' => $namaBulan[(int) $tanggal->format('n')],
                'tahun' => $tanggal->format('Y'),
                'total' => (int) ($dataBulanan[$periode] ?? 0),
            ];
        }

        $nilaiTertinggi = max(array_column($grafikPeminjaman, 'total'));
        $nilaiTerendah = min(array_column($grafikPeminjaman, 'total'));

        $bulanTeramai = $nilaiTertinggi > 0
            ? collect($grafikPeminjaman)->firstWhere('total', $nilaiTertinggi)
            : null;

        $bulanTersepi = $nilaiTertinggi > 0
            ? collect($grafikPeminjaman)->firstWhere('total', $nilaiTerendah)
            : null;

        // Koordinat grafik SVG, dihitung dari data aktual.
        $chartPoints = [];
        $jumlahBulan = count($grafikPeminjaman);

        foreach ($grafikPeminjaman as $index => $bulan) {
            $x = 36 + ($index * (628 / ($jumlahBulan - 1)));
            $y = 166 - (($bulan['total'] / max(1, $nilaiTertinggi)) * 126);

            $chartPoints[] = [
                'x' => round($x, 2),
                'y' => round($y, 2),
                'bulan' => $bulan['bulan'],
                'total' => $bulan['total'],
            ];
        }

        $chartPolyline = implode(' ', array_map(
            fn ($point) => $point['x'] . ',' . $point['y'],
            $chartPoints
        ));

        $chartArea = 'M ' . $chartPoints[0]['x'] . ' 166 L '
            . implode(' L ', array_map(
                fn ($point) => $point['x'] . ' ' . $point['y'],
                $chartPoints
            ))
            . ' L ' . $chartPoints[count($chartPoints) - 1]['x'] . ' 166 Z';

        // Transaksi terbaru untuk tabel aktivitas dashboard.
        $transaksiTerbaru = Transaksi::with(['anggota', 'buku'])
            ->orderByDesc('tanggal_pinjam')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('pages.admin.dashboard', compact(
            'totalAnggota',
            'totalBuku',
            'totalPeminjaman',
            'peminjamanAktif',
            'menungguVerifikasi',
            'grafikPeminjaman',
            'bulanTeramai',
            'bulanTersepi',
            'nilaiTertinggi',
            'chartPoints',
            'chartPolyline',
            'chartArea',
            'transaksiTerbaru'
        ));
    }
}
