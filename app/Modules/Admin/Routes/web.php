<?php

use App\Modules\Admin\Controllers\DashboardController;
use App\Modules\Admin\Controllers\ProductAdminController;
use App\Modules\Admin\Controllers\OrderAdminController;
use App\Modules\Admin\Controllers\UserAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Products management
    Route::resource('products', ProductAdminController::class);

    // Orders management
    Route::get('orders', [OrderAdminController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderAdminController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [OrderAdminController::class, 'updateStatus'])->name('orders.update-status');

    // Users management
    Route::get('users', [UserAdminController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserAdminController::class, 'show'])->name('users.show');
});
