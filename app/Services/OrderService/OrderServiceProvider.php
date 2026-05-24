<?php

namespace App\Providers;

use App\Services\OrderService\OrderService;
use App\Services\OrderService\OrderServiceInterface;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
    }

    public function boot(): void
    {
        //
    }
}