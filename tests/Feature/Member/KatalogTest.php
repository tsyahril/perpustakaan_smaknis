<?php

namespace Tests\Feature\Member;

use App\Models\Buku;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MembuatDataPerpustakaan;
use Tests\TestCase;

class KatalogTest extends TestCase
{
    use MembuatDataPerpustakaan;
    use RefreshDatabase;

    public function test_tamu_bisa_membuka_beranda_katalog_dan_detail_buku(): void
    {
        $buku = $this->buatBuku(['judul' => 'Laskar Pelangi']);

        $this->get('/')->assertOk()->assertSee('Laskar Pelangi');
        $this->get(route('katalog.index'))->assertOk()->assertSee('Laskar Pelangi');
        $this->get(route('katalog.show', $buku))->assertOk()->assertSee('Laskar Pelangi')->assertSee('Deskripsi buku uji.');
        $this->get(route('panduan'))->assertOk()->assertSee('Aturan denda');
    }

    public function test_beranda_kosong_tidak_error(): void
    {
        $this->get('/')->assertOk();
        $this->get(route('katalog.index'))->assertOk()->assertSee('Belum ada buku yang cocok');
    }

    public function test_pencarian_mencocokkan_judul_penulis_dan_kategori(): void
    {
        $this->buatBuku(['judul' => 'Bumi Manusia', 'penulis' => 'Pramoedya']);
        $this->buatBuku(['judul' => 'Atomic Habits', 'penulis' => 'James Clear', 'id_kategori' => $this->buatKategori('Pengembangan Diri')->id]);

        $this->get(route('katalog.index', ['q' => 'bumi']))->assertSee('Bumi Manusia')->assertDontSee('Atomic Habits');
        $this->get(route('katalog.index', ['q' => 'clear']))->assertSee('Atomic Habits')->assertDontSee('Bumi Manusia');
        $this->get(route('katalog.index', ['q' => 'pengembangan']))->assertSee('Atomic Habits')->assertDontSee('Bumi Manusia');
        $this->get(route('katalog.index', ['q' => 'bumi pramoedya']))->assertSee('Bumi Manusia');
    }

    public function test_karakter_like_pada_pencarian_diperlakukan_sebagai_teks_biasa(): void
    {
        $this->buatBuku(['judul' => 'Diskon 100% Ilmu']);
        $this->buatBuku(['judul' => 'Buku Biasa']);

        $this->get(route('katalog.index', ['q' => '%']))->assertSee('Diskon 100% Ilmu')->assertDontSee('Buku Biasa');
    }

    public function test_filter_kategori_ketersediaan_penerbit_dan_tahun(): void
    {
        $fiksi = $this->buatKategori('Fiksi');
        $sains = $this->buatKategori('Sains');
        $this->buatBuku(['judul' => 'Novel Lama', 'id_kategori' => $fiksi->id, 'tahun_terbit' => 2001, 'penerbit' => 'Gramedia']);
        $this->buatBuku(['judul' => 'Novel Habis', 'id_kategori' => $fiksi->id, 'tahun_terbit' => 2022, 'stok' => 0, 'penerbit' => 'Mizan']);
        $this->buatBuku(['judul' => 'Buku Sains Baru', 'id_kategori' => $sains->id, 'tahun_terbit' => 2023, 'penerbit' => 'Mizan']);

        $this->get(route('katalog.index', ['kategori' => $fiksi->id]))
            ->assertSee('Novel Lama')->assertSee('Novel Habis')->assertDontSee('Buku Sains Baru');

        $this->get(route('katalog.index', ['tersedia' => 1]))
            ->assertSee('Novel Lama')->assertDontSee('Novel Habis');

        $this->get(route('katalog.index', ['penerbit' => ['Mizan']]))
            ->assertSee('Novel Habis')->assertSee('Buku Sains Baru')->assertDontSee('Novel Lama');

        $this->get(route('katalog.index', ['tahun_dari' => 2010, 'tahun_sampai' => 2022]))
            ->assertSee('Novel Habis')->assertDontSee('Novel Lama')->assertDontSee('Buku Sains Baru');
    }

    public function test_urutan_judul_dan_kunci_tidak_dikenal(): void
    {
        $this->buatBuku(['judul' => 'Bbb']);
        $this->buatBuku(['judul' => 'Aaa']);

        $this->get(route('katalog.index', ['urut' => 'judul_az']))->assertSeeInOrder(['Aaa', 'Bbb']);
        $this->get(route('katalog.index', ['urut' => 'judul_za']))->assertSeeInOrder(['Bbb', 'Aaa']);
        $this->get(route('katalog.index', ['urut' => 'ngawur; drop table']))->assertOk();
        $this->get(route('katalog.index', ['urut' => 'populer']))->assertOk();
    }

    public function test_parameter_berbentuk_array_atau_aneh_tidak_membuat_error(): void
    {
        $this->buatBuku();

        $this->get('/katalog?q[]=a&urut[]=b&kategori[]=1&tahun_dari=abc&penerbit=teks')->assertOk();
        $this->get('/katalog?page=9999')->assertOk();
        $this->get('/katalog/abc')->assertNotFound();
        $this->get('/katalog/999999')->assertNotFound();
    }

    public function test_saran_pencarian_mengembalikan_buku_dan_kategori(): void
    {
        $this->buatBuku(['judul' => 'Pemrograman Web', 'id_kategori' => $this->buatKategori('Pemrograman')->id]);

        $this->getJson(route('katalog.saran', ['q' => 'pemro']))
            ->assertOk()
            ->assertJsonPath('buku.0.judul', 'Pemrograman Web')
            ->assertJsonPath('kategori.0.nama', 'Pemrograman');

        $this->getJson(route('katalog.saran', ['q' => 'p']))->assertOk()->assertJson(['buku' => [], 'kategori' => []]);
    }

    public function test_buku_terakhir_dilihat_disimpan_di_sesi_dan_tampil_di_beranda(): void
    {
        $buku = $this->buatBuku(['judul' => 'Buku Yang Dilihat']);

        $this->get(route('katalog.show', $buku))->assertOk();

        $this->get('/')->assertOk()->assertSee('Terakhir kamu lihat');
    }

    public function test_tombol_masuk_tampil_untuk_tamu_dan_menu_akun_untuk_anggota(): void
    {
        [$user] = $this->buatAnggota([], ['name' => 'Budi Santoso']);

        $this->get('/')->assertSee('Masuk');
        $this->actingAs($user)->get('/')->assertSee('Budi')->assertSee('Pinjaman saya');
    }

    public function test_beranda_menampilkan_populer_hanya_bila_ada_pinjaman(): void
    {
        $buku = $this->buatBuku();
        [, $anggota] = $this->buatAnggota();

        $this->get('/')->assertDontSee('Paling sering dipinjam</h2>', false);

        Transaksi::create([
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => today(),
            'status' => 'pinjam',
            'status_persetujuan' => 'disetujui',
        ]);

        $this->get('/')->assertSee('Paling sering dipinjam</h2>', false);
        $this->assertSame(1, Buku::denganJumlahDipinjam()->find($buku->id)->dipinjam_count);
    }
}
