<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukus', function (Blueprint $table) {
            $table->id();
            $table->string('cover', 255)->nullable();
            $table->string('judul', 255);
            $table->string('penulis', 150);
            $table->string('penerbit', 150);
            $table->string('isbn', 30)->unique();
            $table->year('tahun_terbit');
            $table->foreignId('id_kategori')->constrained('kategoris')->restrictOnDelete();
            $table->unsignedInteger('stok')->default(0);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukus');
    }
};
