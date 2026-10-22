<?php

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Format tampilan yang dipakai berulang di halaman anggota.
 */
final class Format
{
    public static function rupiah(int|float|null $nilai): string
    {
        return 'Rp '.number_format((float) $nilai, 0, ',', '.');
    }

    public static function tanggal(?CarbonInterface $tanggal, bool $denganHari = false): string
    {
        if (! $tanggal) {
            return '-';
        }

        return $tanggal->copy()->locale('id')->translatedFormat($denganHari ? 'l, d F Y' : 'd F Y');
    }

    public static function tanggalSingkat(?CarbonInterface $tanggal): string
    {
        return $tanggal ? $tanggal->copy()->locale('id')->translatedFormat('d M Y') : '-';
    }

    /**
     * Selisih hari terhadap hari ini: "hari ini", "3 hari lagi", "terlambat 2 hari".
     */
    public static function sisaHari(?CarbonInterface $tanggal): string
    {
        if (! $tanggal) {
            return 'Tanpa batas waktu';
        }

        $selisih = (int) today()->diffInDays($tanggal->copy()->startOfDay(), false);

        return match (true) {
            $selisih === 0 => 'Jatuh tempo hari ini',
            $selisih > 0 => "{$selisih} hari lagi",
            default => 'Terlambat '.abs($selisih).' hari',
        };
    }
}
