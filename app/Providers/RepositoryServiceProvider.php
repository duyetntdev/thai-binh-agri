<?php

namespace App\Providers;

use App\Cart\Cart;
use App\Repositories\Contracts\BlogRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\MenuItemRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\PageRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\BlogRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\MenuItemRepository;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\PageRepository;
use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Binds repository interfaces to their Eloquent implementations.
 * Also registers singleton services shared across the request lifecycle.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    public array $bindings = [
        BlogRepositoryInterface::class => BlogRepository::class,
        MenuItemRepositoryInterface::class => MenuItemRepository::class,
        PageRepositoryInterface::class => PageRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        CategoryRepositoryInterface::class => CategoryRepository::class,
        OrderRepositoryInterface::class => OrderRepository::class,
        UserRepositoryInterface::class => UserRepository::class,
        PaymentRepositoryInterface::class => PaymentRepository::class,
    ];

    public function register(): void
    {
        // Cart is a singleton per request
        $this->app->singleton(Cart::class);
    }

    public function boot(): void
    {
        // Share top-level categories to the main layout header
        View::composer('layouts.app', function ($view) {
            $categories = $this->app->make(CategoryRepositoryInterface::class)->allWithActiveProductCount();

            $view->with(
                'headerCategories',
                $categories->take(5)
            )->with(
                'mobileCategories',
                $categories
            );
        });
    }
}
