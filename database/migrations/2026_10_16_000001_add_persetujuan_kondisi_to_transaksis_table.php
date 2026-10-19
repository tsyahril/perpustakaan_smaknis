<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom persetujuan, grouping multi-buku, kondisi kembali,
     * dan rincian denda ke tabel transaksis (tanpa mengubah kolom lama).
     */
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->string('kode_peminjaman', 30)->nullable()->after('buku_id')->index();
            $table->enum('status_persetujuan', ['diajukan', 'disetujui', 'ditolak'])
                ->default('disetujui')->after('status');
            $table->enum('kondisi', ['baik', 'rusak', 'hilang'])->nullable()->after('status_persetujuan');
            $table->unsignedInteger('hari_terlambat')->default(0)->after('kondisi');
            $table->unsignedBigInteger('denda_terlambat')->default(0)->after('hari_terlambat');
            $table->unsignedBigInteger('denda_kondisi')->default(0)->after('denda_terlambat');
            $table->text('catatan')->nullable()->after('denda');
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn([
                'kode_peminjaman',
                'status_persetujuan',
                'kondisi',
                'hari_terlambat',
                'denda_terlambat',
                'denda_kondisi',
                'catatan',
            ]);
        });
    }
};
