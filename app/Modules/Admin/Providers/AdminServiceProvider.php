<?php

namespace App\Modules\Admin\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('web')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        });

        $this->loadViewsFrom(__DIR__ . '/../Views', 'admin');
    }
}
