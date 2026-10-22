<?php

use App\Http\Controllers\Member\BerandaController;
use App\Http\Controllers\Member\FavoritController;
use App\Http\Controllers\Member\KatalogController;
use App\Http\Controllers\Member\KeranjangController;
use App\Http\Controllers\Member\PanduanController;
use App\Http\Controllers\Member\PinjamanController;
use App\Http\Middleware\BagikanSesiMember;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| TOKO BUKU / KATALOG ANGGOTA
|--------------------------------------------------------------------------
| Katalog bisa dilihat siapa saja. Favorit, keranjang, dan pinjaman
| butuh login.
*/
Route::middleware(BagikanSesiMember::class)->group(function () {

    Route::get('/', [BerandaController::class, 'index'])->name('beranda');

    Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');

    Route::get('/katalog/saran', [KatalogController::class, 'saran'])
        ->middleware('throttle:60,1')
        ->name('katalog.saran');

    Route::get('/katalog/{buku}', [KatalogController::class, 'show'])
        ->whereNumber('buku')
        ->name('katalog.show');

    Route::get('/panduan', [PanduanController::class, 'index'])->name('panduan');

    Route::middleware('auth')->group(function () {

        Route::get('/favorit', [FavoritController::class, 'index'])->name('favorit.index');
        Route::post('/favorit/{buku}', [FavoritController::class, 'toggle'])
            ->whereNumber('buku')
            ->name('favorit.toggle');

        Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
        Route::post('/keranjang/ajukan', [KeranjangController::class, 'checkout'])->name('keranjang.checkout');
        Route::delete('/keranjang', [KeranjangController::class, 'kosongkan'])->name('keranjang.kosongkan');
        Route::post('/keranjang/{buku}', [KeranjangController::class, 'tambah'])
            ->whereNumber('buku')
            ->name('keranjang.tambah');
        Route::delete('/keranjang/{buku}', [KeranjangController::class, 'hapus'])
            ->whereNumber('buku')
            ->name('keranjang.hapus');

        Route::get('/pinjaman-saya', [PinjamanController::class, 'index'])->name('pinjaman.index');
        Route::get('/pinjaman-saya/{groupKey}', [PinjamanController::class, 'show'])
            ->where('groupKey', 'K:[A-Za-z0-9\-]+|T:\d+')
            ->name('pinjaman.show');
        Route::post('/pinjaman-saya/{groupKey}/batal', [PinjamanController::class, 'batal'])
            ->where('groupKey', 'K:[A-Za-z0-9\-]+|T:\d+')
            ->name('pinjaman.batal');
    });
});
