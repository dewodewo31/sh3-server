<?php

namespace App\Providers;

use App\Services\EventServices\EventService;
use App\Services\EventServices\EventServiceInterface;
use App\Services\OrderService\OrderService;
use App\Services\OrderService\OrderServiceInterface;
use App\Services\ParticipantService\ParticipantService;
use App\Services\ParticipantService\ParticipantServiceInterface;
use App\Services\PaymentService\PaymentService;
use App\Services\PaymentService\PaymentServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(EventServiceInterface::class, EventService::class);
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
        $this->app->bind(ParticipantServiceInterface::class, ParticipantService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
