<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ganti kolom string bebas `kelas` menjadi FK `id_kelas` ke tabel `kelas`.
     */
    public function up(): void
    {
        // DROP COLUMN via SQL mentah agar tidak butuh doctrine/dbal.
        if (Schema::hasColumn('anggotas', 'kelas')) {
            DB::statement('ALTER TABLE anggotas DROP COLUMN kelas');
        }

        Schema::table('anggotas', function (Blueprint $table) {
            $table->foreignId('id_kelas')->nullable()->after('nis_nip')
                ->constrained('kelas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('anggotas', function (Blueprint $table) {
            $table->dropForeign(['id_kelas']);
        });

        DB::statement('ALTER TABLE anggotas DROP COLUMN id_kelas');

        Schema::table('anggotas', function (Blueprint $table) {
            $table->string('kelas', 50)->nullable()->after('nis_nip');
        });
    }
};
