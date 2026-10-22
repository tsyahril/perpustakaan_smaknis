<?php

namespace Tests\Concerns;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;

/**
 * Pembuat data uji untuk fitur katalog dan peminjaman anggota.
 * BukuFactory sengaja dikosongkan di project ini, jadi data dibuat langsung.
 */
trait MembuatDataPerpustakaan
{
    private int $urutData = 0;

    protected function buatKategori(string $nama = 'Fiksi'): Kategori
    {
        return Kategori::firstOrCreate(['nama' => $nama]);
    }

    protected function buatBuku(array $atribut = []): Buku
    {
        $this->urutData++;

        return Buku::create(array_merge([
            'judul' => "Buku Uji {$this->urutData}",
            'penulis' => 'Penulis Uji',
            'penerbit' => 'Penerbit Uji',
            'isbn' => sprintf('978%010d', $this->urutData),
            'tahun_terbit' => 2020,
            'id_kategori' => $this->buatKategori()->id,
            'stok' => 3,
            'deskripsi' => 'Deskripsi buku uji.',
        ], $atribut));
    }

    /**
     * @return array{0: User, 1: Anggota}
     */
    protected function buatAnggota(array $anggota = [], array $akun = []): array
    {
        $this->urutData++;

        $kartu = Anggota::create(array_merge([
            'nomor_anggota' => 'UJI'.str_pad((string) $this->urutData, 4, '0', STR_PAD_LEFT),
            'nama' => 'Anggota Uji',
            'jenis_anggota' => 'siswa',
            'nis_nip' => (string) (1000 + $this->urutData),
            'status' => 'aktif',
        ], $anggota));

        $user = User::factory()->create(array_merge([
            'role' => 'siswa',
            'id_anggota' => $kartu->id,
        ], $akun));

        return [$user, $kartu];
    }
}
