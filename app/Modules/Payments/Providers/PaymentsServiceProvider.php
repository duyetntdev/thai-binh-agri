<?php

namespace App\Modules\Payments\Providers;

use App\Modules\Payments\Services\VnpayService;
use Illuminate\Support\ServiceProvider;

class PaymentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(VnpayService::class, fn () => new VnpayService(
            merchantId: config('vnpay.merchant_id'),
            secretKey: config('vnpay.secret_key'),
            paymentUrl: config('vnpay.url'),
            returnUrl: config('vnpay.return_url'),
        ));
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Views', 'payments');
    }
}
