<?php

namespace App\Jobs;

use App\Services\TelemetryProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job: Limpieza periódica de datos de telemetría
 * 
 * Se ejecuta diariamente para:
 * - Eliminar telemetría antigua 
 * - Eliminar GPS tracks antiguos
 * - Deduplicar registros
 * 
 * Programar en App\Console\Kernel:
 * $schedule->job(new CleanTelemetryDataJob)->daily();
 */
class CleanTelemetryDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $daysToKeep;

    /**
     * Create a new job instance.
     */
    public function __construct(int $daysToKeep = 90)
    {
        $this->daysToKeep = $daysToKeep;
    }

    /**
     * Execute the job.
     */
    public function handle(TelemetryProcessingService $telemetryService): void
    {
        Log::info('Iniciando limpieza de telemetría', [
            'days_to_keep' => $this->daysToKeep,
        ]);

        $startTime = microtime(true);

        try {
            // 1. Deduplicar telemetría
            $deduplicated = $telemetryService->deduplicateTelemetry();

            // 2. Limpiar telemetría antigua
            $telemetryDeleted = $telemetryService->cleanOldTelemetry($this->daysToKeep);

            // 3. Limpiar GPS tracks antiguos
            $gpsTracksDeleted = $telemetryService->cleanOldGpsTracks($this->daysToKeep);

            $duration = round(microtime(true) - $startTime, 2);

            Log::info('Limpieza de telemetría completada', [
                'deduplicated' => $deduplicated,
                'telemetry_deleted' => $telemetryDeleted,
                'gps_tracks_deleted' => $gpsTracksDeleted,
                'duration_seconds' => $duration,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error en limpieza de telemetría', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-lanzar excepción para que Laravel maneje el retry
            throw $e;
        }
    }

    /**
     * Número de intentos antes de fallar
     */
    public function tries(): int
    {
        return 3;
    }

    /**
     * Tiempo de espera entre reintentos (segundos)
     */
    public function backoff(): array
    {
        return [60, 300, 900]; // 1min, 5min, 15min
    }
}
