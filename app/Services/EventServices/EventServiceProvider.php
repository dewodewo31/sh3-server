<?php

namespace App\Providers;

use App\Services\EventServices\EventService;
use App\Services\EventServices\EventServiceInterface;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EventServiceInterface::class, EventService::class);
    }

    public function boot(): void
    {
        //
    }
}