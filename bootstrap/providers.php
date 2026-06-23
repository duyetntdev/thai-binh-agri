<?php

return [
    App\Providers\AppServiceProvider::class,
    // Module Service Providers
    App\Modules\Auth\Providers\AuthServiceProvider::class,
    App\Modules\Admin\Providers\AdminServiceProvider::class,
    App\Modules\Home\Providers\HomeServiceProvider::class,
    App\Modules\Cart\Providers\CartServiceProvider::class,
    App\Modules\Products\Providers\ProductsServiceProvider::class,
    App\Modules\Orders\Providers\OrdersServiceProvider::class,
    App\Modules\Payments\Providers\PaymentsServiceProvider::class,
    // Livewire Service Provider
    Livewire\LivewireServiceProvider::class,
];
