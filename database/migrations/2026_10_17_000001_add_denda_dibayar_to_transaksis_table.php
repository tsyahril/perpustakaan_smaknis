<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah penanda lunas denda. Riwayat pengembalian yang masih punya
     * denda belum dibayar tidak boleh dihapus.
     */
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->boolean('denda_dibayar')->default(false)->after('denda_kondisi');
            $table->timestamp('denda_dibayar_pada')->nullable()->after('denda_dibayar');
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn(['denda_dibayar', 'denda_dibayar_pada']);
        });
    }
};