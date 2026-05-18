<?php

use App\Modules\Payments\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('thanh-toan')->name('payments.')->group(function () {
    Route::get('/don-hang/{order}', [PaymentController::class, 'initiate'])->name('initiate');
    Route::get('/callback', [PaymentController::class, 'callback'])->name('callback')->withoutMiddleware('auth');
    Route::get('/ket-qua/{order}', [PaymentController::class, 'result'])->name('result');
});
