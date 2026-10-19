<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tanggal kembali rencana boleh kosong per buku. Peminjam boleh
     * mengisi rencana hanya untuk sebagian buku; sisanya diisi
     * saat proses pengembalian.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE transaksis MODIFY tanggal_kembali_rencana DATE NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE transaksis MODIFY tanggal_kembali_rencana DATE NOT NULL');
    }
};