<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $tingkatan = ['X', 'XI', 'XII'];
        $jurusan = ['TKJ', 'RPL', 'MM', 'AKL', 'OTKP'];

        foreach ($tingkatan as $tingkat) {
            foreach ($jurusan as $kode) {
                Kelas::firstOrCreate(
                    ['nama' => "{$tingkat} {$kode}"],
                    ['tingkat' => $tingkat, 'jurusan' => $kode]
                );
            }
        }
    }
}
