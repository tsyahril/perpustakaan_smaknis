<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Akun anggota (tabel users) butuh dua kolom tambahan:
     * - kelas  : satu kolom dipakai dua kebutuhan, yaitu kelas untuk siswa
     *            dan bagian/jabatan untuk guru (Staf, Guru BK, dll).
     * - id_anggota : tautan ke kartu anggota (tabel anggotas) supaya jumlah
     *            peminjaman bisa ditampilkan di halaman Kelola Anggota.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('kelas', 60)->nullable()->after('role');
            $table->unsignedBigInteger('id_anggota')->nullable()->after('kelas');
            $table->index('id_anggota');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['id_anggota']);
            $table->dropColumn(['kelas', 'id_anggota']);
        });
    }
};