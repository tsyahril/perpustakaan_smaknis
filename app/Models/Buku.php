<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Buku extends Model
{
    use HasFactory;

    protected $fillable = [
        'cover',
        'judul',
        'penulis',
        'penerbit',
        'isbn',
        'tahun_terbit',
        'id_kategori',
        'stok',
        'deskripsi',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function coverUrl(): ?string
    {
        if (! $this->cover) {
            return null;
        }

        // Izinkan URL penuh (misal dari seeder/faker).
        if (str_starts_with($this->cover, 'http://') || str_starts_with($this->cover, 'https://')) {
            return $this->cover;
        }

        $path = ltrim($this->cover, '/');

        // Hindari prefix ganda seperti "storage/storage/..." atau "public/...".
        $path = preg_replace('#^(public/|storage/)#', '', $path);

        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        // Pakai asset() agar mengikuti host yang sedang diakses
        // (tidak tergantung APP_URL di .env).
        return asset('storage/' . $path);
    }
}
