<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProdukController;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
    Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
    Route::get('/produk/{kode_produk}', [ProdukController::class, 'show'])->name('produk.show');
    Route::post('/produk/{kode_produk}/penyesuaian-stok', [ProdukController::class, 'sesuaikanStok'])->name('produk.sesuaikan-stok');
});