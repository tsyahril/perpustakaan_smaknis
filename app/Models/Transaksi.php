<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'anggota_id',
        'buku_id',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_aktual',
        'denda',
        'status',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali_aktual' => 'date',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    /**
     * Hitung denda berdasarkan keterlambatan.
     * Denda: Rp 5.000 per hari terlambat.
     */
    public function calculateDenda(): int
    {
        if ($this->status === 'dikembalikan' && $this->tanggal_kembali_aktual) {
            $terlambat = $this->tanggal_kembali_aktual->diffInDays($this->tanggal_kembali_rencana, false);
            if ($terlambat > 0) {
                return $terlambat * 5000;
            }
        }
        return 0;
    }
}
