<?php

namespace Tests\Feature\Member;

use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MembuatDataPerpustakaan;
use Tests\TestCase;

class PeminjamanMemberTest extends TestCase
{
    use MembuatDataPerpustakaan;
    use RefreshDatabase;

    private function isiKeranjang(User $user, array $bukus): void
    {
        foreach ($bukus as $buku) {
            $this->actingAs($user)->postJson(route('keranjang.tambah', $buku))->assertOk();
        }
    }

    private function ajukan(User $user, ?string $tanggal = null)
    {
        return $this->actingAs($user)->post(route('keranjang.checkout'), [
            'tanggal_kembali' => $tanggal ?? today()->addDays(7)->toDateString(),
        ]);
    }

    public function test_pengajuan_membuat_transaksi_satu_kode_menahan_stok_dan_mengosongkan_keranjang(): void
    {
        [$user, $anggota] = $this->buatAnggota();
        $a = $this->buatBuku(['stok' => 3]);
        $b = $this->buatBuku(['stok' => 1]);
        $this->isiKeranjang($user, [$a, $b]);

        $respons = $this->ajukan($user);

        $transaksis = Transaksi::where('anggota_id', $anggota->id)->get();
        $this->assertCount(2, $transaksis);
        $this->assertCount(1, $transaksis->pluck('kode_peminjaman')->unique());
        $this->assertStringStartsWith('PJM-', $transaksis->first()->kode_peminjaman);
        $this->assertEqualsCanonicalizing(['diajukan'], $transaksis->pluck('status_persetujuan')->unique()->all());
        $this->assertEqualsCanonicalizing(['pinjam'], $transaksis->pluck('status')->unique()->all());
        $this->assertSame(2, $a->fresh()->stok);
        $this->assertSame(0, $b->fresh()->stok);
        $this->assertDatabaseCount('keranjangs', 0);

        $respons->assertRedirect(route('pinjaman.show', 'K:'.$transaksis->first()->kode_peminjaman));
        $this->actingAs($user)->get(route('pinjaman.show', 'K:'.$transaksis->first()->kode_peminjaman))
            ->assertOk()->assertSee('Menunggu persetujuan')->assertSee($a->judul);
    }

    public function test_tanggal_kembali_divalidasi(): void
    {
        [$user] = $this->buatAnggota();
        $this->isiKeranjang($user, [$this->buatBuku()]);

        $this->ajukan($user, today()->toDateString())->assertSessionHasErrors('tanggal_kembali');
        $this->ajukan($user, today()->subDay()->toDateString())->assertSessionHasErrors('tanggal_kembali');
        $this->ajukan($user, today()->addDays(31)->toDateString())->assertSessionHasErrors('tanggal_kembali');
        $this->actingAs($user)->post(route('keranjang.checkout'))->assertSessionHasErrors('tanggal_kembali');
        $this->ajukan($user, 'bukan-tanggal')->assertSessionHasErrors('tanggal_kembali');

        $this->assertDatabaseCount('transaksis', 0);
        $this->ajukan($user, today()->addDays(30)->toDateString())->assertSessionHasNoErrors();
    }

    public function test_keranjang_kosong_ditolak(): void
    {
        [$user] = $this->buatAnggota();

        $this->ajukan($user)->assertSessionHas('error');
        $this->assertDatabaseCount('transaksis', 0);
    }

    public function test_pengajuan_gagal_seluruhnya_bila_satu_buku_kehabisan_stok(): void
    {
        [$user] = $this->buatAnggota();
        $a = $this->buatBuku(['stok' => 5]);
        $b = $this->buatBuku(['stok' => 1]);
        $this->isiKeranjang($user, [$a, $b]);

        // Stok b diambil anggota lain sebelum checkout.
        $b->update(['stok' => 0]);

        $this->ajukan($user)->assertSessionHas('error');

        $this->assertDatabaseCount('transaksis', 0);
        $this->assertSame(5, $a->fresh()->stok, 'Stok buku pertama harus kembali karena transaksi di-rollback.');
        $this->assertDatabaseCount('keranjangs', 2);
    }

    public function test_buku_yang_masih_dipinjam_tidak_bisa_diajukan_lagi(): void
    {
        [$user, $anggota] = $this->buatAnggota();
        $buku = $this->buatBuku();
        $this->isiKeranjang($user, [$buku]);

        Transaksi::create([
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => today(),
            'status' => 'pinjam',
            'status_persetujuan' => 'diajukan',
        ]);

        $this->ajukan($user)->assertSessionHas('error');
        $this->assertDatabaseCount('transaksis', 1);
        $this->assertSame(3, $buku->fresh()->stok);
    }

    public function test_pengajuan_bisa_dibatalkan_dan_stok_kembali(): void
    {
        [$user, $anggota] = $this->buatAnggota();
        $buku = $this->buatBuku(['stok' => 2]);
        $this->isiKeranjang($user, [$buku]);
        $this->ajukan($user);
        $kode = Transaksi::first()->kode_peminjaman;

        $this->assertSame(1, $buku->fresh()->stok);

        $this->actingAs($user)->post(route('pinjaman.batal', 'K:'.$kode))->assertRedirect(route('pinjaman.index', ['status' => 'ditolak']));

        $this->assertSame(2, $buku->fresh()->stok);
        $transaksi = Transaksi::first();
        $this->assertSame('ditolak', $transaksi->status_persetujuan);
        $this->assertSame('Dibatalkan oleh peminjam', $transaksi->catatan);

        $this->actingAs($user)->get(route('pinjaman.show', 'K:'.$kode))->assertSee('Dibatalkan');

        // Membatalkan dua kali tidak boleh menambah stok lagi.
        $this->actingAs($user)->post(route('pinjaman.batal', 'K:'.$kode))->assertSessionHas('error');
        $this->assertSame(2, $buku->fresh()->stok);
    }

    public function test_pengajuan_yang_sudah_disetujui_tidak_bisa_dibatalkan(): void
    {
        [$user] = $this->buatAnggota();
        $buku = $this->buatBuku(['stok' => 2]);
        $this->isiKeranjang($user, [$buku]);
        $this->ajukan($user);
        Transaksi::query()->update(['status_persetujuan' => 'disetujui']);

        $this->actingAs($user)->post(route('pinjaman.batal', 'K:'.Transaksi::first()->kode_peminjaman))->assertSessionHas('error');
        $this->assertSame(1, $buku->fresh()->stok);
    }

    public function test_anggota_lain_tidak_bisa_melihat_atau_membatalkan_pinjaman_orang(): void
    {
        [$pemilik] = $this->buatAnggota();
        [$orangLain] = $this->buatAnggota();
        $this->isiKeranjang($pemilik, [$this->buatBuku()]);
        $this->ajukan($pemilik);
        $kunci = 'K:'.Transaksi::first()->kode_peminjaman;

        $this->actingAs($orangLain)->get(route('pinjaman.show', $kunci))->assertNotFound();
        $this->actingAs($orangLain)->post(route('pinjaman.batal', $kunci))->assertNotFound();
        $this->assertSame('diajukan', Transaksi::first()->status_persetujuan);

        $this->actingAs($orangLain)->get(route('pinjaman.index'))->assertOk()->assertDontSee(Transaksi::first()->kode_peminjaman);
    }

    public function test_kunci_peminjaman_tidak_valid_menghasilkan_404(): void
    {
        [$user] = $this->buatAnggota();

        $this->actingAs($user)->get('/pinjaman-saya/K:tidak-ada')->assertNotFound();
        $this->actingAs($user)->get('/pinjaman-saya/ngawur')->assertNotFound();
        $this->actingAs($user)->get(route('pinjaman.show', 'T:999'))->assertNotFound();
    }

    public function test_alur_persetujuan_admin_tetap_berjalan_untuk_pengajuan_anggota(): void
    {
        [$user] = $this->buatAnggota();
        $admin = User::factory()->create(['role' => 'admin']);
        $buku = $this->buatBuku(['stok' => 2]);
        $this->isiKeranjang($user, [$buku]);
        $this->ajukan($user);
        $kunci = 'K:'.Transaksi::first()->kode_peminjaman;

        $response = $this->actingAs($admin)->get(route('admin.request-peminjaman.index'));
        $this->assertTrue($response->isRedirect());
        // Follow redirect to peminjaman.index
        $this->actingAs($admin)->get(route('admin.peminjaman.index', ['tab' => 'pengajuan']))->assertOk()->assertSee(Transaksi::first()->kode_peminjaman);

        $this->actingAs($admin)->post(route('admin.request-peminjaman.approve', $kunci))->assertRedirect();
        $this->actingAs($user)->get(route('pinjaman.show', $kunci))->assertSee('Sedang dipinjam');
        $this->actingAs($user)->get(route('pinjaman.index', ['status' => 'dipinjam']))->assertSee($buku->judul);
    }

    public function test_penolakan_admin_tampil_dengan_catatan_dan_stok_kembali(): void
    {
        [$user] = $this->buatAnggota();
        $admin = User::factory()->create(['role' => 'admin']);
        $buku = $this->buatBuku(['stok' => 2]);
        $this->isiKeranjang($user, [$buku]);
        $this->ajukan($user);
        $kunci = 'K:'.Transaksi::first()->kode_peminjaman;

        $this->actingAs($admin)->post(route('admin.request-peminjaman.reject', $kunci), ['catatan' => 'Kuota habis'])->assertRedirect();

        $this->assertSame(2, $buku->fresh()->stok);
        $this->actingAs($user)->get(route('pinjaman.show', $kunci))->assertSee('ditolak pustakawan')->assertSee('Kuota habis');
    }

    public function test_pinjaman_terlambat_menampilkan_status_dan_perkiraan_denda(): void
    {
        [$user, $anggota] = $this->buatAnggota();
        $buku = $this->buatBuku();

        Transaksi::create([
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'kode_peminjaman' => 'PJM-UJI-TELAT',
            'tanggal_pinjam' => today()->subDays(10),
            'tanggal_kembali_rencana' => today()->subDays(3),
            'status' => 'pinjam',
            'status_persetujuan' => 'disetujui',
        ]);

        $this->actingAs($user)->get(route('pinjaman.index'))->assertOk()->assertSee('Terlambat 3 hari');
        $this->actingAs($user)->get(route('pinjaman.show', 'K:PJM-UJI-TELAT'))->assertOk()->assertSee('Rincian denda');
        $this->actingAs($user)->get(route('guru.dashboard'))->assertOk();
    }

    public function test_pinjaman_lama_tanpa_kode_tetap_bisa_dibuka(): void
    {
        [$user, $anggota] = $this->buatAnggota();
        $buku = $this->buatBuku();

        $transaksi = Transaksi::create([
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => today()->subDays(2),
            'status' => 'dikembalikan',
            'status_persetujuan' => 'disetujui',
            'tanggal_kembali_aktual' => today(),
        ]);

        $this->actingAs($user)->get(route('pinjaman.index'))->assertOk()->assertSee('Dikembalikan');
        $this->actingAs($user)->get(route('pinjaman.show', $transaksi->group_key))->assertOk()->assertSee($buku->judul);
    }

    public function test_tab_pinjaman_menyaring_status(): void
    {
        [$user, $anggota] = $this->buatAnggota();
        $menunggu = $this->buatBuku(['judul' => 'Buku Menunggu']);
        $kembali = $this->buatBuku(['judul' => 'Buku Kembali']);

        Transaksi::create(['anggota_id' => $anggota->id, 'buku_id' => $menunggu->id, 'kode_peminjaman' => 'PJM-A', 'tanggal_pinjam' => today(), 'status' => 'pinjam', 'status_persetujuan' => 'diajukan']);
        Transaksi::create(['anggota_id' => $anggota->id, 'buku_id' => $kembali->id, 'kode_peminjaman' => 'PJM-B', 'tanggal_pinjam' => today(), 'status' => 'dikembalikan', 'status_persetujuan' => 'disetujui']);

        $this->actingAs($user)->get(route('pinjaman.index', ['status' => 'menunggu']))->assertSee('Buku Menunggu')->assertDontSee('Buku Kembali');
        $this->actingAs($user)->get(route('pinjaman.index', ['status' => 'dikembalikan']))->assertSee('Buku Kembali')->assertDontSee('Buku Menunggu');
        $this->actingAs($user)->get(route('pinjaman.index', ['status' => 'ngawur']))->assertOk()->assertSee('Buku Menunggu')->assertSee('Buku Kembali');
    }
}
