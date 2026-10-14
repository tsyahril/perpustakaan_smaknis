<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_anggota',
        'nama',
        'jenis_anggota',
        'nis_nip',
        'id_kelas',
        'alamat',
        'no_telepon',
        'foto',
        'status',
    ];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
}
