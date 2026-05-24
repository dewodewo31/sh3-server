<?php

namespace App\Providers;

use App\Services\PaymentService\PaymentService;
use App\Services\PaymentService\PaymentServiceInterface;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
    }

    public function boot(): void
    {
        //
    }
}