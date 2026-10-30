<?php

use App\Modules\Blog\Controllers\BlogAdminController;
use App\Modules\Blog\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('/blog', [BlogController::class, 'index'])->name('home.blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('home.blog.show');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('blogs', BlogAdminController::class)->except('show');
});
