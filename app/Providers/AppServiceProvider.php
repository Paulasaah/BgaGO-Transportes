<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MockDataService;
use App\Services\DataService;
use App\Contracts\GeocodingService;
use App\Services\Geocoding\NominatimGeocodingService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registrar el servicio de datos (mock vs real)
        $this->app->singleton('data.service', function ($app) {
            $useMock = config('app.use_mock_data', true);
            return $useMock ? new MockDataService() : new DataService();
        });

        // Registrar servicio de geocodificación
        $this->app->bind(GeocodingService::class, function ($app) {
            // Por ahora solo Nominatim; en el futuro se puede leer config('geocoding.default')
            return new NominatimGeocodingService();
        });
    }

    public function boot(): void
    {
        //
    }
}