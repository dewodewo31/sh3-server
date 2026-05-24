<?php

namespace App\Providers;

use App\Services\ParticipantService\ParticipantService;
use App\Services\ParticipantService\ParticipantServiceInterface;
use Illuminate\Support\ServiceProvider;

class ParticipantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ParticipantServiceInterface::class, ParticipantService::class);
    }

    public function boot(): void
    {
        //
    }
}