<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create kategori (cek duplikat)
        $kategoris = [
            ['nama' => 'Fiksi'],
            ['nama' => 'Non-Fiksi'],
            ['nama' => 'Pelajaran'],
            ['nama' => 'Biografi'],
            ['nama' => 'Komik'],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::firstOrCreate(
                ['nama' => $kategori['nama']],
                $kategori
            );
        }

        // Create buku
        $bukus = [
            [
                'judul' => 'Laskar Pelangi',
                'penulis' => 'Andrea Hirata',
                'penerbit' => 'Bentang Pustaka',
                'isbn' => '978-979-9069-14-8',
                'tahun_terbit' => 2005,
                'id_kategori' => 1,
                'stok' => 5,
                'deskripsi' => 'Novel tentang kisah anak-anak dari keluarga kurang mampu.',
            ],
            [
                'judul' => 'Negeri 5 Menara',
                'penulis' => 'Ahmad Fuadi',
                'penerbit' => 'Gramedia Pustaka Utama',
                'isbn' => '978-979-24-0494-7',
                'tahun_terbit' => 2009,
                'id_kategori' => 1,
                'stok' => 3,
                'deskripsi' => 'Cerita tentang perjuangan pelajar di Pesantren Modern.',
            ],
            [
                'judul' => 'Bumi',
                'penulis' => 'Tere Liye',
                'penerbit' => 'Kompas Gramedia',
                'isbn' => '978-979-766-695-2',
                'tahun_terbit' => 2012,
                'id_kategori' => 1,
                'stok' => 4,
                'deskripsi' => 'Novel fiksi ilmiah tentang kehidupan di planet bumi.',
            ],
            [
                'judul' => 'Sapiens',
                'penulis' => 'Yuval Noah Harari',
                'penerbit' => 'L. A. Hoje',
                'isbn' => '978-972-8952-01-7',
                'tahun_terbit' => 2014,
                'id_kategori' => 2,
                'stok' => 2,
                'deskripsi' => 'Sejarah singkat umat manusia dari Zaman Batu hingga modern.',
            ],
            [
                'judul' => 'Matematika SMA Kelas 10',
                'penulis' => 'Tim Erlangga',
                'penerbit' => 'Erlangga',
                'isbn' => '978-979-015-726-6',
                'tahun_terbit' => 2022,
                'id_kategori' => 3,
                'stok' => 10,
                'deskripsi' => 'Buku pelajaran matematika untuk siswa SMA kelas 10.',
            ],
            [
                'judul' => 'Fisika SMA Kelas 11',
                'penulis' => 'Tim Yudhistira',
                'penerbit' => 'Yudhistira',
                'isbn' => '978-979-021-485-9',
                'tahun_terbit' => 2022,
                'id_kategori' => 3,
                'stok' => 8,
                'deskripsi' => 'Buku pelajaran fisika untuk siswa SMA kelas 11.',
            ],
            [
                'judul' => 'Steve Jobs',
                'penulis' => 'Walter Isaacson',
                'penerbit' => 'Mizan',
                'isbn' => '978-979-433-537-9',
                'tahun_terbit' => 2011,
                'id_kategori' => 4,
                'stok' => 3,
                'deskripsi' => 'Biografi lengkap pendiri Apple, Steve Jobs.',
            ],
            [
                'judul' => 'One Piece Vol. 1',
                'penulis' => 'Eiichiro Oda',
                'penerbit' => 'Elex Media Komputindo',
                'isbn' => '978-979-592-010-8',
                'tahun_terbit' => 2000,
                'id_kategori' => 5,
                'stok' => 6,
                'deskripsi' => 'Manga petualangan tentang pencarian harta karun.',
            ],
            [
                'judul' => 'Naruto Vol. 1',
                'penulis' => 'Masashi Kishimoto',
                'penerbit' => 'Elex Media Komputindo',
                'isbn' => '978-979-592-118-1',
                'tahun_terbit' => 1999,
                'id_kategori' => 5,
                'stok' => 5,
                'deskripsi' => 'Manga tentang ninja muda bernama Naruto.',
            ],
        ];

        foreach ($bukus as $buku) {
            Buku::create($buku);
        }

        // Create anggota
        $anggotas = [
            [
                'nomor_anggota' => 'SIS001',
                'nama' => 'Ahmad Riza Pratama',
                'jenis_anggota' => 'siswa',
                'nis_nip' => '12345001',
                'kelas' => 'X A',
                'alamat' => 'Jl. Merdeka No. 10, Jakarta',
                'no_telepon' => '081234567890',
                'status' => 'aktif',
            ],
            [
                'nomor_anggota' => 'SIS002',
                'nama' => 'Siti Nurhaliza',
                'jenis_anggota' => 'siswa',
                'nis_nip' => '12345002',
                'kelas' => 'X B',
                'alamat' => 'Jl. Sudirman No. 5, Jakarta',
                'no_telepon' => '081234567891',
                'status' => 'aktif',
            ],
            [
                'nomor_anggota' => 'SIS003',
                'nama' => 'Budi Santoso',
                'jenis_anggota' => 'siswa',
                'nis_nip' => '12345003',
                'kelas' => 'XI A',
                'alamat' => 'Jl. Gatot Subroto No. 20, Jakarta',
                'no_telepon' => '081234567892',
                'status' => 'aktif',
            ],
            [
                'nomor_anggota' => 'SIS004',
                'nama' => 'Rina Wijaya',
                'jenis_anggota' => 'siswa',
                'nis_nip' => '12345004',
                'kelas' => 'XI B',
                'alamat' => 'Jl. Ahmad Yani No. 15, Jakarta',
                'no_telepon' => '081234567893',
                'status' => 'aktif',
            ],
            [
                'nomor_anggota' => 'SIS005',
                'nama' => 'Chandra Kusuma',
                'jenis_anggota' => 'siswa',
                'nis_nip' => '12345005',
                'kelas' => 'XII A',
                'alamat' => 'Jl. Imam Bonjol No. 8, Jakarta',
                'no_telepon' => '081234567894',
                'status' => 'aktif',
            ],
            [
                'nomor_anggota' => 'GUR001',
                'nama' => 'Ibu Dwi Hartono',
                'jenis_anggota' => 'guru',
                'nis_nip' => '19700515199203001',
                'kelas' => null,
                'alamat' => 'Jl. Diponegoro No. 25, Jakarta',
                'no_telepon' => '082123456789',
                'status' => 'aktif',
            ],
            [
                'nomor_anggota' => 'GUR002',
                'nama' => 'Pak Suryanto',
                'jenis_anggota' => 'guru',
                'nis_nip' => '19680325198702001',
                'kelas' => null,
                'alamat' => 'Jl. Cendrawasih No. 12, Jakarta',
                'no_telepon' => '082123456790',
                'status' => 'aktif',
            ],
        ];

        foreach ($anggotas as $anggota) {
            Anggota::create($anggota);
        }
    }
}
