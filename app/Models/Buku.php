<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    /**
     * Hanya buku yang masih punya eksemplar untuk dipinjam.
     */
    public function scopeTersedia(Builder $query): Builder
    {
        return $query->where('stok', '>', 0);
    }

    /**
     * Cari berdasarkan judul, penulis, penerbit, ISBN, atau nama kategori.
     * Tiap kata harus cocok di salah satu kolom tersebut.
     */
    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        $kataKunci = preg_split('/\s+/', trim((string) $kata), -1, PREG_SPLIT_NO_EMPTY);

        foreach (array_slice($kataKunci, 0, 5) as $kunci) {
            $pola = '%' . addcslashes($kunci, '%_\\') . '%';

            $query->where(fn (Builder $w) => $w
                ->where('judul', 'like', $pola)
                ->orWhere('penulis', 'like', $pola)
                ->orWhere('penerbit', 'like', $pola)
                ->orWhere('isbn', 'like', $pola)
                ->orWhereHas('kategori', fn (Builder $k) => $k->where('nama', 'like', $pola)));
        }

        return $query;
    }

    /**
     * Tambahkan kolom dipinjam_count: berapa kali buku ini diajukan
     * dan tidak ditolak.
     */
    public function scopeDenganJumlahDipinjam(Builder $query): Builder
    {
        return $query->withCount([
            'transaksis as dipinjam_count' => fn (Builder $t) => $t->where('status_persetujuan', '!=', 'ditolak'),
        ]);
    }

    /**
     * Urutan katalog. Kunci tidak dikenal jatuh ke "terbaru".
     * Untuk "populer" pasangkan dengan scopeDenganJumlahDipinjam.
     */
    public function scopeUrutkan(Builder $query, ?string $urut, ?string $kata = null): Builder
    {
        return match ($urut) {
            'judul_az' => $query->orderBy('judul'),
            'judul_za' => $query->orderByDesc('judul'),
            'tahun_terbaru' => $query->orderByDesc('tahun_terbit')->orderByDesc('id'),
            'tahun_terlama' => $query->orderBy('tahun_terbit')->orderBy('id'),
            'stok' => $query->orderByDesc('stok')->orderBy('judul'),
            'populer' => $query->orderByDesc('dipinjam_count')->orderByDesc('id'),
            'relevan' => $query
                ->orderByRaw('CASE WHEN judul LIKE ? THEN 0 WHEN judul LIKE ? THEN 1 ELSE 2 END', [
                    addcslashes((string) $kata, '%_\\') . '%',
                    '%' . addcslashes((string) $kata, '%_\\') . '%',
                ])
                ->orderByDesc('id'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };
    }

    public function favoritOleh()
    {
        return $this->belongsToMany(User::class, 'favorits')->withTimestamps();
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
