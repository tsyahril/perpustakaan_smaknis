<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke test redesain: memastikan halaman-halaman utama bertema
 * putih-sepia dengan menu gabungan tetap bisa dirender,
 * rute lama tetap redirect, dan pembatasan role berjalan.
 */
class SmokeRenderTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function pustakawan(): User
    {
        return User::factory()->create(['role' => 'pustakawan']);
    }

    private function guru(): User
    {
        return User::factory()->create(['role' => 'guru', 'is_petugas' => false]);
    }

    public function test_admin_bisa_render_halaman_utama(): void
    {
        $admin = $this->admin();

        $urls = [
            route('admin.dashboard'),
            route('admin.buku.index'),
            route('admin.kategori.index'),
            route('admin.kelas.index'),
            route('admin.penulis.index'),
            route('admin.penerbit.index'),
            route('admin.anggota.index'),
            route('admin.peminjaman.index'),
            route('admin.peminjaman.index', ['tab' => 'pengajuan']),
            route('admin.pengembalian.index'),
            route('admin.pengembalian.index', ['tab' => 'pengajuan']),
            route('admin.denda.index'),
            route('admin.denda.index', ['tab' => 'setting']),
        ];

        foreach ($urls as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_pustakawan_bisa_render_halaman_operasional(): void
    {
        $pustakawan = $this->pustakawan();

        $urls = [
            route('pustakawan.dashboard'),
            route('admin.buku.index'),
            route('admin.peminjaman.index'),
            route('admin.peminjaman.index', ['tab' => 'pengajuan']),
            route('admin.pengembalian.index'),
            route('admin.denda.index'),
        ];

        foreach ($urls as $url) {
            $this->actingAs($pustakawan)->get($url)->assertOk();
        }
    }

    public function test_redirect_rute_lama_masih_bekerja(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.request-peminjaman.index'))
            ->assertRedirect(route('admin.peminjaman.index', ['tab' => 'pengajuan']));

        $this->actingAs($admin)->get(route('admin.request-pengembalian.index'))
            ->assertRedirect(route('admin.pengembalian.index', ['tab' => 'pengajuan']));

        $this->actingAs($admin)->get(route('admin.setting-denda.index'))
            ->assertRedirect(route('admin.denda.index', ['tab' => 'setting']));
    }

    public function test_setting_denda_ditolak_untuk_pustakawan(): void
    {
        $pustakawan = $this->pustakawan();

        $this->actingAs($pustakawan)->get(route('admin.denda.index', ['tab' => 'setting']))
            ->assertRedirect(route('admin.denda.index'));
    }

    public function test_guru_biasa_hanya_bisa_dashboard(): void
    {
        $guru = $this->guru();

        $this->actingAs($guru)->get(route('guru.dashboard'))->assertOk();
        $this->actingAs($guru)->get(route('admin.denda.index'))->assertForbidden();
    }
}
