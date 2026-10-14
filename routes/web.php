<?php

use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DendaController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\PeminjamanController;
use App\Http\Controllers\Admin\PengembalianController;
use App\Http\Controllers\Admin\PenerbitController;
use App\Http\Controllers\Admin\PenulisController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'akses.perpus'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/anggota', [AnggotaController::class, 'index'])->name('anggota.index');
    Route::get('/anggota/create', [AnggotaController::class, 'create'])->name('anggota.create');
    Route::post('/anggota', [AnggotaController::class, 'store'])->name('anggota.store');
    Route::get('/anggota/{anggota}/edit', [AnggotaController::class, 'edit'])->name('anggota.edit');
    Route::put('/anggota/{anggota}', [AnggotaController::class, 'update'])->name('anggota.update');
    Route::delete('/anggota/{anggota}', [AnggotaController::class, 'destroy'])->name('anggota.destroy');
    Route::post('/anggota/{anggota}/reset-password', [AnggotaController::class, 'resetPassword'])->name('anggota.reset-password');
    // Setting cepat: ubah guru biasa <-> pustakawan.
    Route::post('/anggota/{anggota}/toggle-akses', [AnggotaController::class, 'toggleAkses'])->name('anggota.toggle-akses');

    Route::resource('buku', BukuController::class)->parameters(['buku' => 'buku']);
    Route::resource('kategori', KategoriController::class)->except(['show'])->parameters(['kategori' => 'kategori']);
    Route::resource('kelas', KelasController::class)->except(['show'])->parameters(['kelas' => 'kelas']);
    Route::get('penulis', [PenulisController::class, 'index'])->name('penulis.index');
    Route::get('penerbit', [PenerbitController::class, 'index'])->name('penerbit.index');

    Route::resource('peminjaman', PeminjamanController::class)->except(['show'])->parameters(['peminjaman' => 'peminjaman']);
    Route::resource('pengembalian', PengembalianController::class)->except(['store', 'destroy'])->parameters(['pengembalian' => 'pengembalian']);
    Route::resource('denda', DendaController::class)->except(['create', 'store', 'edit', 'update', 'destroy'])->parameters(['denda' => 'denda']);
});

Route::middleware('auth')->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';
