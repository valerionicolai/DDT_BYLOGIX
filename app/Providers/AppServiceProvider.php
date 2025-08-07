<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use App\Services\BarcodeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BarcodeService::class, function ($app) {
            return new BarcodeService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
    }
}
