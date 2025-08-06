<?php

namespace App\Providers;

use App\Services\AstacalaApiClient;
use App\Services\AuthService;
use App\Services\DisasterReportService;
use Illuminate\Support\ServiceProvider;

class AstacalaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register API Client as singleton
        $this->app->singleton(AstacalaApiClient::class, function ($app) {
            return new AstacalaApiClient;
        });

        // Register Auth Service
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService($app->make(AstacalaApiClient::class));
        });

        // Register Disaster Report Service
        $this->app->singleton(DisasterReportService::class, function ($app) {
            return new DisasterReportService($app->make(AstacalaApiClient::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
