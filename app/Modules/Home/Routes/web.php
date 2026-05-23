<?php

use App\Modules\Home\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/gioi-thieu', [HomeController::class, 'about'])->name('home.about');
Route::get('/tin-tuc', [HomeController::class, 'news'])->name('home.news');
Route::get('/chinh-sach', [HomeController::class, 'policy'])->name('home.policy');
Route::get('/lien-he', [HomeController::class, 'contact'])->name('home.contact');
