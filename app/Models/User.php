<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLES = ['admin', 'pustakawan', 'guru', 'siswa'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'kelas',
        'id_anggota',
        'is_petugas',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_petugas' => 'boolean',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota');
    }

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
