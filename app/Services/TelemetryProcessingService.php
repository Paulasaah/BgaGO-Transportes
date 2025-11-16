<?php

namespace App\Services;

use App\Actions\Telemetry\ProcessTelemetryAction;
use App\Models\Telemetria;
use App\Models\GpsTrack;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Service: Gestión de telemetría vehicular
 * 
 * Responsabilidades:
 * - Orquestar procesamiento de telemetría
 * - Limpieza y deduplicación de datos
 * - Agregación de métricas
 * - Validación de reglas de negocio
 */
class TelemetryProcessingService
{
    private ProcessTelemetryAction $processTelemetryAction;

    public function __construct(ProcessTelemetryAction $processTelemetryAction)
    {
        $this->processTelemetryAction = $processTelemetryAction;
    }

    /**
     * Procesar mensaje MQTT de telemetría
     * 
     * @param string $topic Topic MQTT
     * @param string $message Payload JSON
     * @return bool
     */
    public function processMqttMessage(string $topic, string $message): bool
    {
        try {
            // 1. Parsear y validar JSON
            $data = json_decode($message, true);
            
            if (!$this->validateTelemetryData($data)) {
                Log::warning('Telemetría inválida recibida', [
                    'topic' => $topic,
                    'message' => substr($message, 0, 200),
                    'validation_failed' => true,
                ]);
                return false;
            }

            // 2. Aplicar throttling en backend (evitar spam)
            if (!$this->shouldProcessTelemetry($data['device_id'])) {
                Log::debug('Telemetría throttled', [
                    'device_id' => $data['device_id'],
                ]);
                return false; // Silenciosamente ignorar (throttled)
            }

            // 3. Procesar telemetría
            $telemetry = $this->processTelemetryAction->execute($data);

            if (!$telemetry) {
                Log::error('ProcessTelemetryAction retornó null', [
                    'device_id' => $data['device_id'],
                ]);
                return false;
            }

            // 4. Verificar alertas (batería baja, mantenimiento, etc.)
            $this->checkAlerts($telemetry);

            Log::info('Telemetría procesada exitosamente', [
                'device_id' => $data['device_id'],
            ]);

            return true;

        } catch (\Throwable $e) {
            Log::error('Error en TelemetryProcessingService', [
                'topic' => $topic,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Validar estructura de datos de telemetría
     */
    private function validateTelemetryData(?array $data): bool
    {
        if (!$data) {
            return false;
        }

        // Campos obligatorios
        $required = ['device_id', 'Geopoint'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return false;
            }
        }

        // Validar Geopoint
        if (!isset($data['Geopoint']['lat']) || !isset($data['Geopoint']['lon'])) {
            return false;
        }

        // Validar rangos
        $lat = $data['Geopoint']['lat'];
        $lon = $data['Geopoint']['lon'];

        if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
            return false;
        }

        return true;
    }

    /**
     * Throttling en backend: máximo 1 actualización por segundo por dispositivo
     */
    private function shouldProcessTelemetry(string $deviceId): bool
    {
        $cacheKey = "telemetry_throttle:{$deviceId}";
        
        if (Cache::has($cacheKey)) {
            return false; // Throttled
        }

        // Marcar como procesado por 1 segundo
        Cache::put($cacheKey, true, 1);
        
        return true;
    }

    /**
     * Verificar alertas críticas
     */
    private function checkAlerts(Telemetria $telemetry): void
    {
        $alerts = [];

        // Batería crítica
        if ($telemetry->battery < 15) {
            $alerts[] = [
                'type' => 'battery_critical',
                'severity' => 'high',
                'message' => "Batería crítica: {$telemetry->battery}%",
            ];
        }

        // Mantenimiento urgente
        if ($telemetry->maintenance_km_left < 50) {
            $alerts[] = [
                'type' => 'maintenance_urgent',
                'severity' => 'medium',
                'message' => "Mantenimiento urgente: {$telemetry->maintenance_km_left} km restantes",
            ];
        }

        // Salud de batería baja
        if ($telemetry->battery_health < 70) {
            $alerts[] = [
                'type' => 'battery_health_low',
                'severity' => 'medium',
                'message' => "Salud de batería baja: {$telemetry->battery_health}%",
            ];
        }

        // Registrar alertas
        if (!empty($alerts)) {
            Log::warning('Alertas de telemetría', [
                'device_id' => $telemetry->device_id,
                'alerts' => $alerts,
            ]);

            // TODO: Enviar notificación a administradores
            // event(new DeviceAlertTriggered($telemetry, $alerts));
        }
    }

    /**
     * Limpieza de telemetría antigua (mantener solo últimos N días)
     * 
     * @param int $daysToKeep Días a mantener (default: 90)
     * @return int Registros eliminados
     */
    public function cleanOldTelemetry(int $daysToKeep = 90): int
    {
        $cutoffDate = now()->subDays($daysToKeep);

        // Mantener solo 1 registro por dispositivo (el más reciente)
        // y eliminar registros antiguos
        $deleted = Telemetria::where('created_at', '<', $cutoffDate)
            ->whereNotIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('telemetrias')
                    ->groupBy('device_id');
            })
            ->delete();

        Log::info('Limpieza de telemetría ejecutada', [
            'days_kept' => $daysToKeep,
            'records_deleted' => $deleted,
        ]);

        return $deleted;
    }

    /**
     * Limpieza de GPS tracks antiguos (mantener solo últimos N días)
     * 
     * @param int $daysToKeep Días a mantener (default: 90)
     * @return int Registros eliminados
     */
    public function cleanOldGpsTracks(int $daysToKeep = 90): int
    {
        $cutoffDate = now()->subDays($daysToKeep);

        $deleted = GpsTrack::where('fecha_registro', '<', $cutoffDate)
            ->delete();

        Log::info('Limpieza de GPS tracks ejecutada', [
            'days_kept' => $daysToKeep,
            'records_deleted' => $deleted,
        ]);

        return $deleted;
    }

    /**
     * Agregación de métricas por hora (para reportes)
     * Agrupa datos de telemetría en ventanas de 1 hora
     * 
     * @param string $deviceId
     * @param \Carbon\Carbon $date
     * @return array
     */
    public function aggregateHourlyMetrics(string $deviceId, \Carbon\Carbon $date): array
    {
        $startOfHour = $date->copy()->startOfHour();
        $endOfHour = $date->copy()->endOfHour();

        $metrics = GpsTrack::where('device_id', $deviceId)
            ->whereBetween('fecha_registro', [$startOfHour, $endOfHour])
            ->selectRaw('
                COUNT(*) as total_points,
                AVG(velocidad) as avg_speed,
                MAX(velocidad) as max_speed,
                AVG(nivel_bateria) as avg_battery,
                MIN(nivel_bateria) as min_battery,
                SUM(CASE WHEN motor_encendido = 1 THEN 1 ELSE 0 END) as time_active
            ')
            ->first();

        return [
            'device_id' => $deviceId,
            'hour' => $startOfHour->toDateTimeString(),
            'total_points' => $metrics->total_points ?? 0,
            'avg_speed' => round($metrics->avg_speed ?? 0, 2),
            'max_speed' => round($metrics->max_speed ?? 0, 2),
            'avg_battery' => round($metrics->avg_battery ?? 0, 1),
            'min_battery' => $metrics->min_battery ?? 100,
            'time_active_minutes' => round(($metrics->time_active ?? 0) * 2 / 60, 1), // Asumiendo 2s por punto
        ];
    }

    /**
     * Deduplicar registros duplicados en telemetría
     * (por si acaso hay duplicados por errores de red)
     * 
     * @return int Registros eliminados
     */
    public function deduplicateTelemetry(): int
    {
        // Mantener solo el registro más reciente por device_id
        $duplicates = DB::select("
            SELECT device_id, COUNT(*) as count
            FROM telemetrias
            GROUP BY device_id
            HAVING count > 1
        ");

        $deleted = 0;

        foreach ($duplicates as $dup) {
            // Mantener solo el más reciente
            $keepId = Telemetria::where('device_id', $dup->device_id)
                ->orderByDesc('created_at')
                ->value('id');

            $deleted += Telemetria::where('device_id', $dup->device_id)
                ->where('id', '!=', $keepId)
                ->delete();
        }

        if ($deleted > 0) {
            Log::info('Deduplicación de telemetría ejecutada', [
                'records_deleted' => $deleted,
            ]);
        }

        return $deleted;
    }
}
