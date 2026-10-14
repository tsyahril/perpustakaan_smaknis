<?php

namespace Database\Factories;

use App\Models\Buku;
use Illuminate\Database\Eloquent\Factories\Factory;

class BukuFactory extends Factory
{
    protected $model = Buku::class;

    /**
     * Factory dikosongkan sesuai permintaan.
     * Semua data dummy dibuat langsung di Database\Seeders\BukuSeeder
     * agar tiap master (buku, kategori, penulis, penerbit) punya 100 data.
     */
    public function definition(): array
    {
        return [];
    }
}
