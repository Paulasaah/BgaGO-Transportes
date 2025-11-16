<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

/**
 * Provider: Broadcasting Service
 * 
 * Responsabilidad: Registrar canales de broadcasting para eventos en tiempo real
 * Habilita la transmisión de eventos vía Reverb/WebSockets
 */
class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::routes();

        require base_path('routes/channels.php');
    }
}
