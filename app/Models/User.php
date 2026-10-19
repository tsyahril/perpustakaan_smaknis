<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Role yang tersedia: admin, pustakawan, guru, siswa.
     */
    public const ROLES = ['admin', 'pustakawan', 'guru', 'siswa'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        // Satu kolom untuk dua kebutuhan: siswa = kelas, guru = bagian/jabatan.
        'kelas',
        // Tautan ke kartu anggota supaya jumlah peminjaman bisa ditampilkan.
        'id_anggota',
        'is_petugas',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_petugas' => 'boolean',
    ];

    /**
     * Kartu anggota yang terhubung dengan akun ini.
     */
    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota');
    }

    /**
     * Akses perpustakaan: admin & pustakawan.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPustakawan(): bool
    {
        return $this->role === 'pustakawan'
            || ($this->isGuru() && (bool) $this->is_petugas);
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function isGuruBiasa(): bool
    {
        return $this->isGuru() && ! (bool) $this->is_petugas;
    }

    public function canManageLibrary(): bool
    {
        return $this->isAdmin() || $this->isPustakawan();
    }

    public function aksesLabel(): string
    {
        if ($this->isAdmin()) {
            return 'Admin';
        }

        if ($this->isPustakawan()) {
            return 'Pustakawan';
        }

        if ($this->isGuru()) {
            return 'Guru';
        }

        return 'Siswa';
    }
}