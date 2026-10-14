<?php

use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\PenerbitController;
use App\Http\Controllers\Admin\PenulisController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/anggota', [AnggotaController::class, 'index'])->name('anggota.index');
    Route::get('/anggota/create', [AnggotaController::class, 'create'])->name('anggota.create');
    Route::post('/anggota', [AnggotaController::class, 'store'])->name('anggota.store');
    Route::get('/anggota/{anggota}/edit', [AnggotaController::class, 'edit'])->name('anggota.edit');
    Route::put('/anggota/{anggota}', [AnggotaController::class, 'update'])->name('anggota.update');
    Route::delete('/anggota/{anggota}', [AnggotaController::class, 'destroy'])->name('anggota.destroy');
    Route::post('/anggota/{anggota}/reset-password', [AnggotaController::class, 'resetPassword'])->name('anggota.reset-password');

    Route::resource('buku', BukuController::class)->parameters(['buku' => 'buku']);
    Route::resource('kategori', KategoriController::class)->except(['show'])->parameters(['kategori' => 'kategori']);
    Route::get('penulis', [PenulisController::class, 'index'])->name('penulis.index');
    Route::get('penerbit', [PenerbitController::class, 'index'])->name('penerbit.index');
});

require __DIR__.'/auth.php';
