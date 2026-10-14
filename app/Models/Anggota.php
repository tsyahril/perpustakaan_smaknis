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
        'kelas',
        'alamat',
        'no_telepon',
        'foto',
        'status',
    ];
}
