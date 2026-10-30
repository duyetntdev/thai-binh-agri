<?php

use App\Http\Middleware\DatabaseTransaction;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Modules\Admin\Providers\AdminServiceProvider;
use App\Modules\Auth\Providers\AuthServiceProvider;
use App\Modules\Blog\Providers\BlogServiceProvider;
use App\Modules\Cart\Providers\CartServiceProvider;
use App\Modules\Home\Providers\HomeServiceProvider;
use App\Modules\Orders\Providers\OrdersServiceProvider;
use App\Modules\Payments\Providers\PaymentsServiceProvider;
use App\Modules\Products\Providers\ProductsServiceProvider;
use App\Providers\RepositoryServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        // Repository bindings — interface → Eloquent implementation
        RepositoryServiceProvider::class,

        // Module service providers
        HomeServiceProvider::class,
        BlogServiceProvider::class,
        AuthServiceProvider::class,
        CartServiceProvider::class,
        ProductsServiceProvider::class,
        OrdersServiceProvider::class,
        PaymentsServiceProvider::class,
        AdminServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(prepend: [
            DatabaseTransaction::class,
        ]);
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
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
