<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MockDataService;
use App\Services\DataService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registrar el servicio
        $this->app->singleton('data.service', function ($app) {
            $useMock = config('app.use_mock_data', true);
            return $useMock ? new MockDataService() : new DataService();
        });

        // Registrar el alias de clase
        $this->app->alias('data.service', MockDataService::class);
    }

    public function boot(): void
    {
        //
    }
}