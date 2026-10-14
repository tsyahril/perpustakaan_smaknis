<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use App\Services\BukuCoverGenerator;
use Illuminate\Database\Seeder;

class BukuSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan kategori tersedia dulu (dibuat via KategoriSeeder).
        if (Kategori::count() === 0) {
            $this->call(KategoriSeeder::class);
        }

        $kategoriIds = Kategori::pluck('id')->all();
        $existingJudul = Buku::pluck('judul')->all();
        $existingPenulis = Buku::pluck('penulis')->all();
        $existingPenerbit = Buku::pluck('penerbit')->all();
        $existingIsbn = Buku::pluck('isbn')->all();

        // Bangun 100 nama penulis & penerbit unik khusus seeder (tanpa factory).
        $daftarPenulis = $this->daftarPenulis($existingPenulis);
        $daftarPenerbit = $this->daftarPenerbit($existingPenerbit);

        $kurang = max(0, 100 - Buku::count());

        // Prioritaskan kategori yang belum punya buku agar 100 kategori terpakai semua.
        $terpakai = Buku::distinct()->pluck('id_kategori')->all();
        $belumTerpakai = array_values(array_diff($kategoriIds, $terpakai));
        shuffle($belumTerpakai);
        $acakKategori = $kategoriIds;
        shuffle($acakKategori);
        $urutanKategori = array_merge($belumTerpakai, $acakKategori);

        for ($i = 0; $i < $kurang; $i++) {
            $judul = $this->judulUnik($existingJudul);
            $existingJudul[] = $judul;

            $penulis = $daftarPenulis[$i % count($daftarPenulis)];
            $penerbit = $daftarPenerbit[$i % count($daftarPenerbit)];

            $isbn = $this->isbnUnik($existingIsbn);
            $existingIsbn[] = $isbn;

            $buku = Buku::create([
                'cover' => null,
                'judul' => $judul,
                'penulis' => $penulis,
                'penerbit' => $penerbit,
                'isbn' => $isbn,
                'tahun_terbit' => fake()->numberBetween(1990, (int) date('Y')),
                'id_kategori' => $urutanKategori[$i % count($urutanKategori)],
                'stok' => fake()->numberBetween(1, 20),
                'deskripsi' => fake()->sentence(20),
            ]);

            // Langsung buatkan cover dummy terkompresi.
            $buku->cover = BukuCoverGenerator::generate($buku);
            $buku->save();
        }

        // Normalisasi: pastikan 100 buku pertama punya 100 kategori,
        // 100 penulis, 100 penerbit & 100 judul unik (penting bila DB sudah berisi dummy lama).
        $bukus = Buku::orderBy('id')->take(100)->get();
        if ($bukus->count() === 100) {
            $penulisSegar = $this->daftarPenulis([]);
            $penerbitSegar = $this->daftarPenerbit([]);
            $kategoriUrut = Kategori::orderBy('id')->pluck('id')->all();
            $judulTerlihat = [];
            foreach ($bukus as $index => $buku) {
                $kategoriBaru = $kategoriUrut[$index % count($kategoriUrut)];
                // Judul duplikat dari dummy lama -> buatkan yang unik.
                $judulBaru = $buku->judul;
                if (in_array($judulBaru, $judulTerlihat, true)) {
                    $judulBaru = $this->judulUnik(array_merge(Buku::pluck('judul')->all(), $judulTerlihat));
                }
                $judulTerlihat[] = $judulBaru;
                $butuhUbah = $buku->penulis !== $penulisSegar[$index]
                    || $buku->penerbit !== $penerbitSegar[$index]
                    || (int) $buku->id_kategori !== (int) $kategoriBaru
                    || $buku->judul !== $judulBaru;
                if ($butuhUbah) {
                    $buku->judul = $judulBaru;
                    $buku->penulis = $penulisSegar[$index];
                    $buku->penerbit = $penerbitSegar[$index];
                    $buku->id_kategori = $kategoriBaru;
                    $buku->cover = null; // regenerate di bawah agar nama penulis sinkron
                    $buku->save();
                }
            }
        }

        // Isi cover yang masih kosong (data lama) dengan cover dummy.
        foreach (Buku::whereNull('cover')->cursor() as $buku) {
            $buku->cover = BukuCoverGenerator::generate($buku);
            $buku->save();
        }
    }

    /**
     * 100 penulis unik (tidak bentrok dengan data yang sudah ada).
     */
    private function daftarPenulis(array $existing): array
    {
        $inti = [
            'Andrea Hirata', 'Pramoedya Ananta Toer', 'Ahmad Tohari', 'Marah Rusli',
            'Tere Liye', 'Dee Lestari', 'Pidi Baiq', 'Ahmad Fuadi',
            'Habiburrahman El Shirazy', 'Hamka', 'Eka Kurniawan', 'Leila S. Chudori',
            'Seno Gumira Ajidarma', 'Raditya Dika', 'Ika Natassa', 'Ilana Tan',
            'Ayu Utami', 'Okky Madasari', 'Ratna Indraswari', 'Gol A Gong',
            'Joko Pinurbo', 'Sapardi Djoko Damono', 'Chairil Anwar', 'Putu Wijaya',
            'Sutardji Calzoum Bachri', 'Rendra', 'Taufiq Ismail', 'Goenawan Mohamad',
        ];

        $depan = ['Andi', 'Budi', 'Citra', 'Dewi', 'Eko', 'Fitri', 'Gilang', 'Hesti', 'Irfan', 'Joko', 'Kirana', 'Lukman', 'Maya', 'Nadia', 'Putri', 'Rizky', 'Sari', 'Teguh', 'Utami', 'Wahyu', 'Yoga', 'Zahra', 'Farhan', 'Gita', 'Hendra', 'Indah', 'Bayu', 'Lestari', 'Dimas', 'Rani'];
        $belakang = ['Pratama', 'Santoso', 'Wijaya', 'Kusuma', 'Saputra', 'Nugroho', 'Rahmawati', 'Setiawan', 'Hidayat', 'Kurniawan', 'Anggraini', 'Maharani', 'Gunawan', 'Puspita', 'Ramadhan', 'Firmansyah', 'Handayani', 'Saputri', 'Nugraha', 'Pramudya', 'Wulandari', 'Hakim', 'Siregar', 'Nasution', 'Pangestu'];

        $hasil = [];
        foreach ($inti as $nama) {
            if (! in_array($nama, $existing, true) && ! in_array($nama, $hasil, true)) {
                $hasil[] = $nama;
            }
        }

        $i = 0;
        while (count($hasil) < 100) {
            $nama = $depan[$i % count($depan)] . ' ' . $belakang[intdiv($i, count($depan)) % count($belakang)];
            if (! in_array($nama, $existing, true) && ! in_array($nama, $hasil, true)) {
                $hasil[] = $nama;
            }
            $i++;
            // Pengaman bila kombinasi habis.
            if ($i > 2000) {
                $nama = 'Penulis Nusantara ' . str_pad((string) (count($hasil) + 1), 3, '0', STR_PAD_LEFT);
                if (! in_array($nama, $existing, true) && ! in_array($nama, $hasil, true)) {
                    $hasil[] = $nama;
                }
            }
        }

        return array_slice($hasil, 0, 100);
    }

    /**
     * 100 penerbit unik (tidak bentrok dengan data yang sudah ada).
     */
    private function daftarPenerbit(array $existing): array
    {
        $inti = [
            'Gramedia Pustaka Utama', 'Bentang Pustaka', 'Mizan', 'Erlangga',
            'Andi Offset', 'Grasindo', 'Republika Penerbit', 'Elex Media Komputindo',
            'Kepustakaan Populer Gramedia', 'Deepublish', 'Yudhistira', 'Tiga Serangkai',
            'Agromedia', 'Anak Hebat Indonesia', 'Bhuana Ilmu Populer', 'Kompas Media',
            'Lentera Hati', 'Pustaka Al-Kautsar', 'Pustaka Jaya', 'Rosda Karya',
        ];

        $hasil = [];
        foreach ($inti as $nama) {
            if (! in_array($nama, $existing, true) && ! in_array($nama, $hasil, true)) {
                $hasil[] = $nama;
            }
        }

        $kota = ['Jakarta', 'Bandung', 'Yogyakarta', 'Surabaya', 'Medan', 'Makassar', 'Semarang', 'Malang', 'Bogor', 'Depok'];
        $i = 0;
        while (count($hasil) < 100) {
            $nama = 'Penerbit Nusantara ' . str_pad((string) (count($hasil) + 1), 3, '0', STR_PAD_LEFT) . ' ' . $kota[$i % count($kota)];
            if (! in_array($nama, $existing, true) && ! in_array($nama, $hasil, true)) {
                $hasil[] = $nama;
            }
            $i++;
        }

        return array_slice($hasil, 0, 100);
    }

    private function judulUnik(array $existing): string
    {
        $awal = ['Laskar', 'Bumi', 'Langit', 'Samudra', 'Cahaya', 'Bayang', 'Rindu', 'Senja', 'Pagi', 'Malam', 'Jejak', 'Aroma', 'Gema', 'Pelita', 'Wisata', 'Kisah', 'Mimpi', 'Harapan', 'Perahu', 'Negeri'];
        $akhir = ['Pelangi', 'Manusia', 'Cinta', 'Rantau', 'Mimpi', 'Harapan', 'Kertas', 'Menara', 'Samudra', 'Bintang', 'Kopi', 'Hujan', 'Rindu', 'Senja', 'Purnama', 'Wijaya', 'Nusantara', 'Pelajar', 'Bangsa', 'Cendekia'];

        do {
            $judul = fake()->randomElement($awal) . ' ' . fake()->randomElement($akhir) . ' ' . fake()->numberBetween(1, 999);
        } while (in_array($judul, $existing, true));

        return $judul;
    }

    private function isbnUnik(array $existing): string
    {
        do {
            $isbn = '978602' . str_pad((string) fake()->numberBetween(0, 9999999), 7, '0', STR_PAD_LEFT);
        } while (in_array($isbn, $existing, true));

        return $isbn;
    }
}
