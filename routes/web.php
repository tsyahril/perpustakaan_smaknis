<?php

use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\Admin\DashboardController;
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
});

require __DIR__.'/auth.php';
