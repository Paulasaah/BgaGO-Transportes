<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DataService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar el servicio REAL (sin mock)
        $this->app->singleton('data.service', function ($app) {
            return new DataService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Nada más por ahora
    }
}
