<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Configurar rate limiting para diferentes endpoints
     */
    protected function configureRateLimiting(): void
    {
        // Rate limit general para API (60 requests por minuto)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Demasiadas solicitudes. Por favor, intenta de nuevo en unos momentos.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });

        // Rate limit estricto para autenticación (5 intentos por minuto)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Demasiados intentos de autenticación. Intenta de nuevo en ' . ($headers['Retry-After'] ?? 60) . ' segundos.',
                    ], 429, $headers);
                });
        });

        // Rate limit para creación de reservas (10 por minuto)
        RateLimiter::for('reservations', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Límite de creación de reservas alcanzado. Espera un momento.',
                    ], 429, $headers);
                });
        });

        // Rate limit para pagos (5 por minuto - crítico)
        RateLimiter::for('payments', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Límite de operaciones de pago alcanzado. Por seguridad, espera un momento.',
                    ], 429, $headers);
                });
        });

        // Rate limit para consultas públicas (120 por minuto)
        RateLimiter::for('public', function (Request $request) {
            return Limit::perMinute(120)
                ->by($request->ip());
        });
    }
}
