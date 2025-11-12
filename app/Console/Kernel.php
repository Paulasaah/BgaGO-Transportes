<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Registra los comandos de Artisan.
     *
     * Aquí puedes registrar manualmente tus comandos personalizados.
     */
    protected $commands = [
        \App\Console\Commands\MqttListen::class,
    ];

    /**
     * Define el cron o tareas programadas si las usas.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Ejemplo: $schedule->command('mqtt:listen')->everyMinute();
    }

    /**
     * Registra los comandos automáticamente desde el directorio.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}
