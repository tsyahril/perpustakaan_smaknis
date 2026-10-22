<?php

namespace Tests\Feature\Member;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MembuatDataPerpustakaan;
use Tests\TestCase;

class AkunMemberTest extends TestCase
{
    use MembuatDataPerpustakaan;
    use RefreshDatabase;

    public function test_dashboard_anggota_menampilkan_kartu_anggota(): void
    {
        [$user, $anggota] = $this->buatAnggota(['nama' => 'Siti Aminah']);

        $this->actingAs($user)->get(route('guru.dashboard'))
            ->assertOk()->assertSee($anggota->nomor_anggota)->assertSee('Anggota aktif');
    }

    public function test_dashboard_menjelaskan_akun_yang_belum_punya_kartu(): void
    {
        $user = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($user)->get(route('guru.dashboard'))->assertOk()->assertSee('belum terhubung dengan kartu anggota');
        $this->actingAs($user)->get(route('pinjaman.index'))->assertOk();
        $this->actingAs($user)->get(route('favorit.index'))->assertOk();
    }

    public function test_guru_petugas_melihat_tautan_panel(): void
    {
        [$guru] = $this->buatAnggota(['jenis_anggota' => 'guru'], ['role' => 'guru', 'is_petugas' => true]);

        $this->actingAs($guru)->get(route('guru.dashboard'))
            ->assertOk()->assertSee('akses pustakawan')->assertSee(route('pustakawan.dashboard'), false);
    }

    public function test_anggota_yang_sudah_login_membuka_login_diarahkan_ke_akunnya_bukan_403(): void
    {
        [$user] = $this->buatAnggota();

        $this->actingAs($user)->get('/login')->assertRedirect(route('guru.dashboard'));
    }

    public function test_keluar_mengembalikan_ke_beranda(): void
    {
        [$user] = $this->buatAnggota();

        $this->actingAs($user)->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_ubah_kata_sandi_dari_halaman_akun(): void
    {
        [$user] = $this->buatAnggota([], ['password' => bcrypt('password-lama')]);

        $this->actingAs($user)->from(route('guru.dashboard'))->put(route('password.update'), [
            'current_password' => 'password-lama',
            'password' => 'password-baru-123',
            'password_confirmation' => 'password-baru-123',
        ])->assertRedirect(route('guru.dashboard'))->assertSessionHas('status', 'password-updated');
    }

    public function test_admin_tetap_bisa_membuka_dashboard_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get('/')->assertOk()->assertSee('Panel Admin');
    }
}
