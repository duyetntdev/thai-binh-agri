<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Support\Facades\URL::forceScheme('http');
    }
}
