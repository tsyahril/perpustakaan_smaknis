<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder utama untuk anggota. Tidak ada lagi seeder anggota terpisah:
 * setiap anggota dibuatkan sekalian akun login-nya, jadi user = anggota
 * (satu kartu anggota + satu akun, saling tertaut lewat users.id_anggota).
 *
 * Isinya:
 * 1. Empat akun sistem: admin, pustakawan, guru, siswa.
 * 2. 100 anggota dummy, masing-masing sudah punya akun.
 * 3. Penyambungan kartu anggota dari seeder lain yang belum punya akun.
 *
 * Password semua akun: password.
 * Idempoten lewat email / nomor_anggota, jadi aman dijalankan ulang.
 */
class UserSeeder extends Seeder
{
    /** Jumlah anggota dummy per role (total 100 orang). */
    private const JUMLAH = [
        'siswa' => 80,
        'guru' => 15,
        'pustakawan' => 3,
        'admin' => 2,
    ];

    /** Bagian/jabatan untuk yang bukan siswa, memakai kolom yang sama dengan kelas. */
    private const BAGIAN = ['Guru Kelas', 'Guru BK', 'Staf', 'Kepala Sekolah', 'Tata Usaha'];

    public function run(): void
    {
        $this->akunSistem();
        $this->anggotaDummy();
        $this->sambungkanKartuLama();
    }

    /**
     * Akun sistem untuk login awal. Tidak punya kartu anggota.
     */
    protected function akunSistem(): void
    {
        $users = [
            ['email' => 'admin@gmail.com', 'name' => 'Admin', 'role' => 'admin'],
            ['email' => 'pustakawan@gmail.com', 'name' => 'Pustakawan', 'role' => 'pustakawan'],
            // Guru memakai kolom yang sama dengan kelas, isinya bagian/jabatan.
            ['email' => 'guru@gmail.com', 'name' => 'Guru', 'role' => 'guru', 'kelas' => 'Guru BK'],
            ['email' => 'siswa@gmail.com', 'name' => 'Siswa', 'role' => 'siswa', 'kelas' => 'X A'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'role' => $user['role'],
                    'kelas' => $user['kelas'] ?? null,
                ]
            );
        }
    }

    /**
     * 100 anggota dummy, satu kartu anggota + satu akun per orang.
     * Butuh tabel kelas sudah terisi, jadi dijalankan setelah KelasSeeder.
     */
    protected function anggotaDummy(): void
    {
        $kelas = Kelas::orderBy('id')->get();

        if ($kelas->isEmpty()) {
            $this->command->warn('Tabel kelas kosong, lewati 100 anggota dummy.');

            return;
        }

        $namaDepan = [
            'Ahmad', 'Siti', 'Budi', 'Rina', 'Dewi', 'Agus', 'Rudi', 'Maya',
            'Andi', 'Fitri', 'Hendra', 'Nina', 'Yudi', 'Lina', 'Dedi',
            'Ratna', 'Eko', 'Wulan', 'Fajar', 'Intan',
        ];

        $namaBelakang = [
            'Pratama', 'Nurhaliza', 'Santoso', 'Wijaya', 'Kusuma', 'Saputra', 'Hidayat', 'Putri',
            'Nugroho', 'Rahmawati', 'Setiawan', 'Anggraini', 'Firmansyah', 'Utami', 'Ramadhan',
            'Maharani', 'Gunawan', 'Lestari', 'Purnama', 'Handoko',
        ];

        $jalan = [
            'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Gatot Subroto', 'Jl. Ahmad Yani',
            'Jl. Imam Bonjol', 'Jl. Diponegoro', 'Jl. Cendrawasih', 'Jl. Pahlawan',
        ];

        $password = Hash::make('password');
        $jumlah = 0;
        $keSiswa = 0;
        $keGuru = 0;

        foreach (self::JUMLAH as $role => $total) {
            for ($i = 0; $i < $total; $i++) {
                $siswa = $role === 'siswa';
                $ke = $siswa ? $keSiswa++ : $keGuru++;

                $nomor = ($siswa ? 'SIS' : 'GUR') . str_pad((string) (100 + $ke), 3, '0', STR_PAD_LEFT);

                $nama = $namaDepan[$jumlah % count($namaDepan)] . ' ' . $namaBelakang[($jumlah * 7) % count($namaBelakang)];
                if (! $siswa) {
                    $nama = ($ke % 2 === 0 ? 'Pak ' : 'Ibu ') . $nama;
                }

                // Siswa pakai kelas, selain itu pakai bagian/jabatan.
                $kelasDipakai = $siswa
                    ? $kelas[$jumlah % $kelas->count()]->nama
                    : self::BAGIAN[$jumlah % count(self::BAGIAN)];

                $anggota = Anggota::firstOrCreate(
                    ['nomor_anggota' => $nomor],
                    [
                        'nama' => $nama,
                        'jenis_anggota' => $siswa ? 'siswa' : 'guru',
                        'nis_nip' => $siswa
                            ? (string) (20001001 + $jumlah)
                            : '19700101' . str_pad((string) (100001 + $jumlah), 10, '0', STR_PAD_LEFT),
                        'id_kelas' => $siswa ? $kelas[$jumlah % $kelas->count()]->id : null,
                        'alamat' => $jalan[$jumlah % count($jalan)] . ' No. ' . (1 + ($jumlah % 99)) . ', Jakarta',
                        'no_telepon' => ($siswa ? '0812' : '0821') . str_pad((string) (10000000 + $jumlah), 8, '0', STR_PAD_LEFT),
                        'status' => 'aktif',
                    ]
                );

                User::updateOrCreate(
                    ['email' => $role . $i . '@gmail.com'],
                    [
                        'name' => $nama,
                        'password' => $password,
                        'role' => $role,
                        'kelas' => $role === 'admin' ? null : $kelasDipakai,
                        'id_anggota' => $anggota->id,
                    ]
                );

                $jumlah++;
            }
        }

        $this->command->info("UserSeeder: {$jumlah} anggota + {$jumlah} akun dibuat.");
    }

    /**
     * Kartu anggota dari seeder lain yang belum punya akun, sambungkan
     * supaya tidak ada anggota tanpa user.
     */
    protected function sambungkanKartuLama(): void
    {
        $password = Hash::make('password');
        $disambung = 0;

        foreach (Anggota::whereDoesntHave('user')->get() as $a) {
            $siswa = $a->jenis_anggota === 'siswa';
            $bagian = self::BAGIAN[$disambung % count(self::BAGIAN)];

            User::updateOrCreate(
                ['email' => strtolower($a->nomor_anggota) . '@gmail.com'],
                [
                    'name' => $a->nama,
                    'password' => $password,
                    'role' => $siswa ? 'siswa' : 'guru',
                    'kelas' => $siswa ? ($a->kelas?->nama ?? $bagian) : $bagian,
                    'id_anggota' => $a->id,
                ]
            );

            $disambung++;
        }

        if ($disambung > 0) {
            $this->command->info("UserSeeder: {$disambung} kartu anggota lama disambung ke akunnya.");
        }
    }
}