<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Login berpindah dari email ke username (untuk semua role:
 * admin, pustakawan, guru, dan siswa). Username lama diisi otomatis
 * dari bagian depan email (admin@gmail.com -> admin) supaya data
 * yang sudah ada tetap bisa login tanpa isi ulang.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
        });

        $terpakai = [];

        foreach (DB::table('users')->orderBy('id')->get() as $user) {
            $dasar = Str::lower(Str::before((string) $user->email, '@'));
            $dasar = preg_replace('/[^a-z0-9._-]/', '', $dasar) ?: 'user';

            $username = $dasar;
            $langkah = 1;

            while (isset($terpakai[$username])) {
                $username = $dasar . '.' . $langkah++;
            }

            $terpakai[$username] = true;

            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
