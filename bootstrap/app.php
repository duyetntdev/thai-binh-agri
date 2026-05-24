<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        // Repository bindings — interface → Eloquent implementation
        \App\Providers\RepositoryServiceProvider::class,

        // Module service providers
        \App\Modules\Home\Providers\HomeServiceProvider::class,
        \App\Modules\Auth\Providers\AuthServiceProvider::class,
        \App\Modules\Cart\Providers\CartServiceProvider::class,
        \App\Modules\Products\Providers\ProductsServiceProvider::class,
        \App\Modules\Orders\Providers\OrdersServiceProvider::class,
        \App\Modules\Payments\Providers\PaymentsServiceProvider::class,
        \App\Modules\Admin\Providers\AdminServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                    Request::HEADER_X_FORWARDED_HOST |
                    Request::HEADER_X_FORWARDED_PORT |
                    Request::HEADER_X_FORWARDED_PROTO
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
