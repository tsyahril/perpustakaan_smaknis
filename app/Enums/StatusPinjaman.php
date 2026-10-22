<?php

namespace App\Enums;

/**
 * Status satu peminjaman (kumpulan buku dengan kode yang sama) di sisi anggota.
 * Diturunkan dari kolom status dan status_persetujuan pada tabel transaksis.
 */
enum StatusPinjaman: string
{
    case Menunggu = 'menunggu';
    case Dipinjam = 'dipinjam';
    case Terlambat = 'terlambat';
    case Dikembalikan = 'dikembalikan';
    case Ditolak = 'ditolak';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu persetujuan',
            self::Dipinjam => 'Sedang dipinjam',
            self::Terlambat => 'Terlambat',
            self::Dikembalikan => 'Dikembalikan',
            self::Ditolak => 'Ditolak',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    /**
     * Kelas Tailwind untuk lencana status.
     */
    public function kelasLencana(): string
    {
        return match ($this) {
            self::Menunggu => 'bg-amber-50 text-amber-700 ring-amber-200',
            self::Dipinjam => 'bg-sky-50 text-sky-700 ring-sky-200',
            self::Terlambat => 'bg-red-50 text-red-700 ring-red-200',
            self::Dikembalikan => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            self::Ditolak, self::Dibatalkan => 'bg-slate-100 text-slate-600 ring-slate-200',
        };
    }
}
