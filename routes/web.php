<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Module routes are loaded by their respective ServiceProviders.
| See app/Modules/*/Providers/*ServiceProvider.php
|--------------------------------------------------------------------------
*/

// Health check
Route::get('/up', fn () => response()->json(['status' => 'ok']));

// Module routes are registered via ServiceProviders:
// - Auth    → app/Modules/Auth/Routes/web.php
// - Products → app/Modules/Products/Routes/web.php
// - Orders  → app/Modules/Orders/Routes/web.php
// - Payments → app/Modules/Payments/Routes/web.php
// - Admin   → app/Modules/Admin/Routes/web.php
