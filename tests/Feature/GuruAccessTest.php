<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_biasa_tidak_bisa_akses_area_admin(): void
    {
        $guru = User::factory()->create(['role' => 'guru', 'is_petugas' => false]);

        $this->actingAs($guru)->get(route('admin.dashboard'))
            ->assertRedirect(route('guru.dashboard'));

        $this->actingAs($guru)->get(route('guru.dashboard'))
            ->assertOk();
    }

    public function test_pustakawan_bisa_akses_area_admin(): void
    {
        $pustakawan = User::factory()->create(['role' => 'guru', 'is_petugas' => true]);

        $this->actingAs($pustakawan)->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_admin_bisa_akses_area_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_toggle_akses_guru_berubah(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru', 'is_petugas' => false]);

        $this->actingAs($admin)->post(route('admin.anggota.toggle-akses', $guru))
            ->assertRedirect();

        $this->assertTrue($guru->fresh()->isPustakawan());
    }

    public function test_toggle_akses_ditolak_untuk_non_guru(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($admin)->post(route('admin.anggota.toggle-akses', $siswa))
            ->assertRedirect();

        $this->assertFalse($siswa->fresh()->is_petugas);
    }
}
