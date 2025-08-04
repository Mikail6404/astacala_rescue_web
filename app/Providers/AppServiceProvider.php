<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AstacalaApiClient;
use App\Services\AuthService;
use App\Services\DisasterReportService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register AstacalaApiClient as singleton
        $this->app->singleton(AstacalaApiClient::class, function ($app) {
            return new AstacalaApiClient();
        });

        // Register AuthService
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService($app->make(AstacalaApiClient::class));
        });

        // Register DisasterReportService
        $this->app->singleton(DisasterReportService::class, function ($app) {
            return new DisasterReportService($app->make(AstacalaApiClient::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
