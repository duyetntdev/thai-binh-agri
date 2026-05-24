<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Trust the Nginx proxy and use X-Forwarded-* headers
        // This ensures the app generates the correct URL scheme (http/https)
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
