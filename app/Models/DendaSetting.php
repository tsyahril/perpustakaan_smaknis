<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DendaSetting extends Model
{
    protected $fillable = [
        'denda_terlambat_per_hari',
        'denda_rusak',
        'denda_hilang',
    ];

    /**
     * Ambil satu baris setting, buat default bila belum ada.
     */
    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'denda_terlambat_per_hari' => 5000,
                'denda_rusak' => 50000,
                'denda_hilang' => 100000,
            ]
        );
    }
}
