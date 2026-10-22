<?php

namespace Tests\Feature\Member;

use App\Models\Transaksi;
use App\Models\User;
use App\Services\PengajuanPeminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MembuatDataPerpustakaan;
use Tests\TestCase;

class KoleksiMemberTest extends TestCase
{
    use MembuatDataPerpustakaan;
    use RefreshDatabase;

    public function test_tamu_diarahkan_ke_login_untuk_halaman_anggota(): void
    {
        $buku = $this->buatBuku();

        foreach (['favorit.index', 'keranjang.index', 'pinjaman.index'] as $rute) {
            $this->get(route($rute))->assertRedirect(route('login'));
        }

        $this->post(route('favorit.toggle', $buku))->assertRedirect(route('login'));
        $this->postJson(route('keranjang.tambah', $buku))->assertUnauthorized();
    }

    public function test_favorit_bisa_ditambah_dan_dilepas(): void
    {
        [$user] = $this->buatAnggota();
        $buku = $this->buatBuku();

        $this->actingAs($user)->postJson(route('favorit.toggle', $buku))
            ->assertOk()->assertJson(['aktif' => true, 'jumlah' => 1]);
        $this->actingAs($user)->get(route('favorit.index'))->assertOk()->assertSee($buku->judul);

        $this->actingAs($user)->postJson(route('favorit.toggle', $buku))
            ->assertOk()->assertJson(['aktif' => false, 'jumlah' => 0]);
        $this->assertDatabaseCount('favorits', 0);
    }

    public function test_favorit_tanpa_javascript_kembali_ke_halaman_sebelumnya(): void
    {
        [$user] = $this->buatAnggota();
        $buku = $this->buatBuku();

        $this->actingAs($user)->from(route('katalog.index'))->post(route('favorit.toggle', $buku))
            ->assertRedirect(route('katalog.index'))->assertSessionHas('success');
    }

    public function test_favorit_milik_pengguna_lain_tidak_bocor(): void
    {
        [$a] = $this->buatAnggota();
        [$b] = $this->buatAnggota();
        $buku = $this->buatBuku(['judul' => 'Rahasia A']);

        $this->actingAs($a)->postJson(route('favorit.toggle', $buku));

        $this->actingAs($b)->get(route('favorit.index'))->assertDontSee('Rahasia A');
    }

    public function test_buku_masuk_keranjang_dan_penambahan_ganda_tidak_menduplikasi(): void
    {
        [$user] = $this->buatAnggota();
        $buku = $this->buatBuku();

        $this->actingAs($user)->postJson(route('keranjang.tambah', $buku))->assertOk()->assertJson(['ok' => true, 'jumlah' => 1]);
        $this->actingAs($user)->postJson(route('keranjang.tambah', $buku))->assertOk()->assertJson(['jumlah' => 1]);

        $this->assertDatabaseCount('keranjangs', 1);
        $this->actingAs($user)->get(route('keranjang.index'))->assertOk()->assertSee($buku->judul);
    }

    public function test_pinjam_sekarang_membawa_ke_halaman_keranjang(): void
    {
        [$user] = $this->buatAnggota();
        $buku = $this->buatBuku();

        $this->actingAs($user)->post(route('keranjang.tambah', $buku), ['langsung' => 1])
            ->assertRedirect(route('keranjang.index'));
    }

    public function test_stok_habis_tidak_bisa_masuk_keranjang(): void
    {
        [$user] = $this->buatAnggota();
        $buku = $this->buatBuku(['stok' => 0]);

        $this->actingAs($user)->postJson(route('keranjang.tambah', $buku))
            ->assertStatus(422)->assertJson(['ok' => false]);
        $this->assertDatabaseCount('keranjangs', 0);
    }

    public function test_keranjang_dibatasi_maksimal_lima_buku(): void
    {
        [$user] = $this->buatAnggota();

        for ($i = 0; $i < PengajuanPeminjaman::MAKS_BUKU; $i++) {
            $this->actingAs($user)->postJson(route('keranjang.tambah', $this->buatBuku()))->assertOk();
        }

        $this->actingAs($user)->postJson(route('keranjang.tambah', $this->buatBuku()))
            ->assertStatus(422)->assertJsonPath('ok', false);
        $this->assertDatabaseCount('keranjangs', PengajuanPeminjaman::MAKS_BUKU);
    }

    public function test_akun_tanpa_kartu_anggota_atau_nonaktif_tidak_bisa_menambah_keranjang(): void
    {
        $buku = $this->buatBuku();

        $tanpaKartu = User::factory()->create(['role' => 'siswa']);
        $this->actingAs($tanpaKartu)->postJson(route('keranjang.tambah', $buku))->assertStatus(422);
        $this->actingAs($tanpaKartu)->get(route('keranjang.index'))->assertOk()->assertSee('belum terhubung dengan kartu anggota');

        [$nonaktif] = $this->buatAnggota(['status' => 'nonaktif']);
        $this->actingAs($nonaktif)->postJson(route('keranjang.tambah', $buku))->assertStatus(422);
    }

    public function test_buku_yang_sedang_dipinjam_tidak_bisa_masuk_keranjang(): void
    {
        [$user, $anggota] = $this->buatAnggota();
        $buku = $this->buatBuku();

        Transaksi::create([
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => today(),
            'status' => 'pinjam',
            'status_persetujuan' => 'disetujui',
        ]);

        $this->actingAs($user)->postJson(route('keranjang.tambah', $buku))->assertStatus(422);
    }

    public function test_hapus_dan_kosongkan_keranjang(): void
    {
        [$user] = $this->buatAnggota();
        $a = $this->buatBuku();
        $b = $this->buatBuku();

        $this->actingAs($user)->postJson(route('keranjang.tambah', $a));
        $this->actingAs($user)->postJson(route('keranjang.tambah', $b));

        $this->actingAs($user)->delete(route('keranjang.hapus', $a))->assertRedirect();
        $this->assertDatabaseCount('keranjangs', 1);

        $this->actingAs($user)->delete(route('keranjang.kosongkan'))->assertRedirect();
        $this->assertDatabaseCount('keranjangs', 0);
    }

    public function test_buku_dihapus_admin_ikut_hilang_dari_favorit_dan_keranjang(): void
    {
        [$user] = $this->buatAnggota();
        $buku = $this->buatBuku();

        $this->actingAs($user)->postJson(route('favorit.toggle', $buku));
        $this->actingAs($user)->postJson(route('keranjang.tambah', $buku));

        $buku->delete();

        $this->assertDatabaseCount('favorits', 0);
        $this->assertDatabaseCount('keranjangs', 0);
    }
}
