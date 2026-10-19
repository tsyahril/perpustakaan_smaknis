<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            KategoriSeeder::class,
            // Harus jalan sebelum UserSeeder: 100 anggota dummy memakai daftar kelas.
            KelasSeeder::class,
            BukuSeeder::class,
            // Kartu anggota lama; akunnya disambung oleh UserSeeder di bawah.
            DummyDataSeeder::class,
            // Sumber utama anggota: kartu anggota + akun login sekaligus (user = anggota).
            // Harus setelah semua kartu anggota sudah ada supaya semuanya dapat akun.
            UserSeeder::class,
        ]);
    }
}