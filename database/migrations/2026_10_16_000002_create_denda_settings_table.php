<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel tunggal untuk setting denda (satu baris aktif).
     */
    public function up(): void
    {
        Schema::create('denda_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('denda_terlambat_per_hari')->default(5000);
            $table->unsignedBigInteger('denda_rusak')->default(50000);
            $table->unsignedBigInteger('denda_hilang')->default(100000);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denda_settings');
    }
};
