<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Kelas;
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

        // Create buku (idempoten via isbn; kategori dicari by nama agar tidak tergantung urutan id)
        $bukus = [
            [
                'judul' => 'Laskar Pelangi',
                'penulis' => 'Andrea Hirata',
                'penerbit' => 'Bentang Pustaka',
                'isbn' => '978-979-9069-14-8',
                'tahun_terbit' => 2005,
                'kategori' => 'Fiksi',
                'stok' => 5,
                'deskripsi' => 'Novel tentang kisah anak-anak dari keluarga kurang mampu.',
            ],
            [
                'judul' => 'Negeri 5 Menara',
                'penulis' => 'Ahmad Fuadi',
                'penerbit' => 'Gramedia Pustaka Utama',
                'isbn' => '978-979-24-0494-7',
                'tahun_terbit' => 2009,
                'kategori' => 'Fiksi',
                'stok' => 3,
                'deskripsi' => 'Cerita tentang perjuangan pelajar di Pesantren Modern.',
            ],
            [
                'judul' => 'Bumi',
                'penulis' => 'Tere Liye',
                'penerbit' => 'Kompas Gramedia',
                'isbn' => '978-979-766-695-2',
                'tahun_terbit' => 2012,
                'kategori' => 'Fiksi',
                'stok' => 4,
                'deskripsi' => 'Novel fiksi ilmiah tentang kehidupan di planet bumi.',
            ],
            [
                'judul' => 'Sapiens',
                'penulis' => 'Yuval Noah Harari',
                'penerbit' => 'L. A. Hoje',
                'isbn' => '978-972-8952-01-7',
                'tahun_terbit' => 2014,
                'kategori' => 'Non-Fiksi',
                'stok' => 2,
                'deskripsi' => 'Sejarah singkat umat manusia dari Zaman Batu hingga modern.',
            ],
            [
                'judul' => 'Matematika SMA Kelas 10',
                'penulis' => 'Tim Erlangga',
                'penerbit' => 'Erlangga',
                'isbn' => '978-979-015-726-6',
                'tahun_terbit' => 2022,
                'kategori' => 'Pelajaran',
                'stok' => 10,
                'deskripsi' => 'Buku pelajaran matematika untuk siswa SMA kelas 10.',
            ],
            [
                'judul' => 'Fisika SMA Kelas 11',
                'penulis' => 'Tim Yudhistira',
                'penerbit' => 'Yudhistira',
                'isbn' => '978-979-021-485-9',
                'tahun_terbit' => 2022,
                'kategori' => 'Pelajaran',
                'stok' => 8,
                'deskripsi' => 'Buku pelajaran fisika untuk siswa SMA kelas 11.',
            ],
            [
                'judul' => 'Steve Jobs',
                'penulis' => 'Walter Isaacson',
                'penerbit' => 'Mizan',
                'isbn' => '978-979-433-537-9',
                'tahun_terbit' => 2011,
                'kategori' => 'Biografi',
                'stok' => 3,
                'deskripsi' => 'Biografi lengkap pendiri Apple, Steve Jobs.',
            ],
            [
                'judul' => 'One Piece Vol. 1',
                'penulis' => 'Eiichiro Oda',
                'penerbit' => 'Elex Media Komputindo',
                'isbn' => '978-979-592-010-8',
                'tahun_terbit' => 2000,
                'kategori' => 'Komik',
                'stok' => 6,
                'deskripsi' => 'Manga petualangan tentang pencarian harta karun.',
            ],
            [
                'judul' => 'Naruto Vol. 1',
                'penulis' => 'Masashi Kishimoto',
                'penerbit' => 'Elex Media Komputindo',
                'isbn' => '978-979-592-118-1',
                'tahun_terbit' => 1999,
                'kategori' => 'Komik',
                'stok' => 5,
                'deskripsi' => 'Manga tentang ninja muda bernama Naruto.',
            ],
        ];

        foreach ($bukus as $buku) {
            $kategoriNama = $buku['kategori'];
            unset($buku['kategori']);
            $buku['id_kategori'] = Kategori::firstOrCreate(['nama' => $kategoriNama])->id;
            Buku::firstOrCreate(['isbn' => $buku['isbn']], $buku);
        }

        // Create anggota (idempoten via nomor_anggota; string kelas dipetakan ke tabel kelas)
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
            $kelasNama = $anggota['kelas'] ?? null;
            unset($anggota['kelas']);
            if ($kelasNama) {
                $parts = explode(' ', $kelasNama, 2);
                $anggota['id_kelas'] = Kelas::firstOrCreate(
                    ['nama' => $kelasNama],
                    ['tingkat' => $parts[0], 'jurusan' => $parts[1] ?? null]
                )->id;
            }
            Anggota::firstOrCreate(['nomor_anggota' => $anggota['nomor_anggota']], $anggota);
        }
    }
}
