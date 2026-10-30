<?php

use App\Modules\Cart\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::prefix('gio-hang')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::get('/tinh-thanh/{province}/phuong-xa', [CartController::class, 'wards'])->name('wards');
    Route::post('/them/{productId}', [CartController::class, 'add'])->name('add');
    Route::patch('/cap-nhat/{productId}', [CartController::class, 'update'])->name('update');
    Route::delete('/xoa/{productId}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
});
