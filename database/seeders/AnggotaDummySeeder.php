<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Tambahan data dummy anggota: setiap anggota langsung dibuatkan
 * akun login-nya (satu kartu anggota = satu akun, saling tertaut
 * lewat users.id_anggota), sama seperti pola UserSeeder.
 *
 * Nomor anggota memakai rentang SIS501+/GUR501+ supaya tidak bentrok
 * dengan dummy dari UserSeeder (SIS100+, GUR100+).
 * Password semua akun: password. Idempoten via nomor_anggota/email.
 */
class AnggotaDummySeeder extends Seeder
{
    /** Daftar anggota: [nomor, nama, jenis, nis_nip, kelas/bagian, alamat, telepon] */
    private const ANGGOTA = [
        ['SIS501', 'Farhan Abdillah', 'siswa', '20005501', 'X TKJ', 'Jl. Kemang Raya No. 3, Jakarta', '081211002201'],
        ['SIS502', 'Nabila Putri Ramadhani', 'siswa', '20005502', 'X RPL', 'Jl. Tebet Barat Dalam No. 12, Jakarta', '081211002202'],
        ['SIS503', 'Rizky Maulana', 'siswa', '20005503', 'XI MM', 'Jl. Cikini Raya No. 45, Jakarta', '081211002203'],
        ['SIS504', 'Alya Salsabila', 'siswa', '20005504', 'XI AKL', 'Jl. Matraman Raya No. 8, Jakarta', '081211002204'],
        ['SIS505', 'Bagas Ramadhan Putra', 'siswa', '20005505', 'XII OTKP', 'Jl. Salemba Raya No. 21, Jakarta', '081211002205'],
        ['SIS506', 'Citra Ayu Lestari', 'siswa', '20005506', 'X TKJ', 'Jl. Paseban Raya No. 7, Jakarta', '081211002206'],
        ['SIS507', 'Dimas Aryasatya', 'siswa', '20005507', 'XI RPL', 'Jl. Kramat Raya No. 19, Jakarta', '081211002207'],
        ['SIS508', 'Elina Maharani', 'siswa', '20005508', 'XII MM', 'Jl. Senen Raya No. 33, Jakarta', '081211002208'],
        ['GUR501', 'Pak Hendra Wijaya', 'guru', '197504122005011002', 'Guru Kelas', 'Jl. Pramuka Raya No. 10, Jakarta', '082133004401'],
        ['GUR502', 'Ibu Ratna Kusumawati', 'guru', '197809272009022003', 'Guru BK', 'Jl. Gunung Sahari No. 5, Jakarta', '082133004402'],
        ['GUR503', 'Pak Agus Setiawan', 'guru', '198212052011011004', 'Staf', 'Jl. Mangga Dua Raya No. 2, Jakarta', '082133004403'],
        ['GUR504', 'Ibu Siti Nurjanah', 'guru', '198503182014032005', 'Kepala Sekolah', 'Jl. Hayam Wuruk No. 60, Jakarta', '082133004404'],
    ];

    public function run(): void
    {
        $password = Hash::make('password');
        $kelas = Kelas::orderBy('id')->get();
        $dibuat = 0;

        foreach (self::ANGGOTA as [$nomor, $nama, $jenis, $nisNip, $kelasAtauBagian, $alamat, $telepon]) {
            $siswa = $jenis === 'siswa';

            // Siswa menempel ke baris kelas; kalau tabel kelas kosong, buat dulu.
            $idKelas = null;
            if ($siswa) {
                $baris = $kelas->firstWhere('nama', $kelasAtauBagian);

                if (! $baris) {
                    [$tingkat, $jurusan] = explode(' ', $kelasAtauBagian, 2);
                    $baris = Kelas::firstOrCreate(
                        ['nama' => $kelasAtauBagian],
                        ['tingkat' => $tingkat, 'jurusan' => $jurusan]
                    );
                }

                $idKelas = $baris->id;
            }

            $anggota = Anggota::firstOrCreate(
                ['nomor_anggota' => $nomor],
                [
                    'nama' => $nama,
                    'jenis_anggota' => $jenis,
                    'nis_nip' => $nisNip,
                    'id_kelas' => $idKelas,
                    'alamat' => $alamat,
                    'no_telepon' => $telepon,
                    'status' => 'aktif',
                ]
            );

            User::updateOrCreate(
                ['email' => strtolower($nomor) . '@gmail.com'],
                [
                    'name' => $nama,
                    'password' => $password,
                    'role' => $siswa ? 'siswa' : 'guru',
                    // Satu kolom untuk dua kebutuhan: kelas siswa / bagian guru.
                    'kelas' => $kelasAtauBagian,
                    'id_anggota' => $anggota->id,
                ]
            );

            $dibuat++;
        }

        $this->command->info("AnggotaDummySeeder: {$dibuat} anggota + akunloginnya siap (password: password).");
    }
}
