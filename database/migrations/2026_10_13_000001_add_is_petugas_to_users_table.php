<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_petugas')) {
                // Penanda akses khusus untuk role guru:
                // true  = guru sebagai petugas, false = guru biasa.
                // NULL / false untuk role selain guru diabaikan.
                $table->boolean('is_petugas')->default(false)->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_petugas');
        });
    }
};
