<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        // 12 kategori bawaan (tetap dipertahankan).
        $bawaan = ['Fiksi', 'Non-Fiksi', 'Pelajaran', 'Referensi', 'Majalah', 'Novel', 'Komik', 'Biografi', 'Sains', 'Sejarah', 'Anak', 'Agama'];

        foreach ($bawaan as $nama) {
            Kategori::firstOrCreate(['nama' => $nama]);
        }

        // Tambahan dummy sampai total 100 kategori (idempoten).
        $tambahan = [
            'Fiksi Ilmiah', 'Fantasi', 'Misteri', 'Thriller', 'Horor', 'Romansa', 'Petualangan',
            'Drama', 'Puisi', 'Cerpen', 'Dongeng', 'Legenda', 'Mitos', 'Fabel',
            'Ensiklopedia', 'Kamus', 'Atlas', 'Jurnal', 'Skripsi', 'Tesis',
            'Matematika', 'Fisika', 'Kimia', 'Biologi', 'Geografi', 'Ekonomi',
            'Sosiologi', 'Antropologi', 'Psikologi', 'Filsafat', 'Hukum', 'Politik',
            'Teknologi', 'Komputer', 'Informatika', 'Otomotif', 'Elektronika', 'Arsitektur',
            'Kedokteran', 'Keperawatan', 'Farmasi', 'Pertanian', 'Peternakan', 'Perikanan',
            'Seni Rupa', 'Seni Musik', 'Seni Tari', 'Fotografi', 'Film', 'Desain',
            'Olahraga', 'Kesehatan', 'Kuliner', 'Pariwisata', 'Bahasa Indonesia', 'Bahasa Inggris',
            'Bahasa Arab', 'Bahasa Jepang', 'PPKn', 'PAI', 'PJOK', 'Prakarya',
            'Bimbingan Konseling', 'Sejarah Indonesia', 'Sejarah Dunia', 'Geografi Dunia',
            'Astronomi', 'Lingkungan', 'Energi', 'Motivasi', 'Pengembangan Diri', 'Kepemimpinan',
            'Bisnis', 'Manajemen', 'Akuntansi', 'Kewirausahaan', 'Robotik', 'AI Dasar',
            'Dongeng Anak', 'Cerita Rakyat', 'Pantun', 'Syair', 'Hadits', 'Tafsir',
            'Fiqih', 'Akhlak', 'Sirah Nabawiyah',
        ];

        foreach ($tambahan as $nama) {
            if (Kategori::count() >= 100) {
                break;
            }
            Kategori::firstOrCreate(['nama' => $nama]);
        }

        // Pengaman: kalau masih kurang dari 100, isi dengan penomoran.
        $i = 1;
        while (Kategori::count() < 100) {
            Kategori::firstOrCreate(['nama' => 'Kategori Dummy ' . str_pad((string) $i, 3, '0', STR_PAD_LEFT)]);
            $i++;
        }
    }
}
