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
use App\Http\Controllers\Admin\RequestPeminjamanController;
use App\Http\Controllers\Admin\RequestPengembalianController;
use App\Http\Controllers\Admin\SettingDendaController;
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

    Route::resource('buku', BukuController::class)->parameters(['buku' => 'buku']);
    Route::resource('kategori', KategoriController::class)->except(['show'])->parameters(['kategori' => 'kategori']);
    Route::resource('kelas', KelasController::class)->except(['show'])->parameters(['kelas' => 'kelas']);
    Route::get('penulis', [PenulisController::class, 'index'])->name('penulis.index');
    Route::get('penerbit', [PenerbitController::class, 'index'])->name('penerbit.index');

    Route::resource('peminjaman', PeminjamanController::class)->except(['show'])->parameters(['peminjaman' => 'peminjaman']);
    // Request peminjaman (pengajuan): folder terpisah dari peminjaman aktif.
    Route::get('request-peminjaman', [RequestPeminjamanController::class, 'index'])->name('request-peminjaman.index');
    Route::post('request-peminjaman/{groupKey}/approve', [RequestPeminjamanController::class, 'approve'])->name('request-peminjaman.approve');
    Route::post('request-peminjaman/{groupKey}/reject', [RequestPeminjamanController::class, 'reject'])->name('request-peminjaman.reject');

    // Pengembalian: riwayat sudah kembali, dikelompokkan per peminjaman.
    Route::get('pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
    Route::post('pengembalian/hapus-massal', [PengembalianController::class, 'destroySelected'])->name('pengembalian.destroy-selected');
    Route::get('pengembalian/{groupKey}', [PengembalianController::class, 'show'])->name('pengembalian.show');
    Route::get('pengembalian/{groupKey}/edit', [PengembalianController::class, 'edit'])->name('pengembalian.edit');
    Route::put('pengembalian/{groupKey}', [PengembalianController::class, 'update'])->name('pengembalian.update');
    Route::delete('pengembalian/{groupKey}', [PengembalianController::class, 'destroy'])->name('pengembalian.destroy');

    // Request pengembalian (menunggu verifikasi): folder terpisah dari riwayat pengembalian.
    Route::get('request-pengembalian', [RequestPengembalianController::class, 'index'])->name('request-pengembalian.index');

    Route::resource('denda', DendaController::class)->except(['create', 'store', 'edit', 'update', 'destroy'])->parameters(['denda' => 'denda']);
    Route::post('denda/{denda}/bayar', [DendaController::class, 'bayar'])->name('denda.bayar');
    Route::get('denda/tagihan/{anggota}', [DendaController::class, 'tagihanAnggota'])->name('denda.tagihan');

    // Setting denda: folder terpisah dari denda.
    Route::get('setting-denda', [SettingDendaController::class, 'index'])->name('setting-denda.index');
    Route::put('setting-denda', [SettingDendaController::class, 'update'])->name('setting-denda.update');
});

Route::middleware('auth')->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';