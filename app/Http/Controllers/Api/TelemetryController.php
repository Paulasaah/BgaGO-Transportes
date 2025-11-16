<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Telemetria;
use App\Models\GpsTrack;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para consultas de telemetría
 * 
 * IMPORTANTE: Este controlador es SOLO LECTURA.
 * La escritura de telemetría se hace vía MQTT Listener.
 * 
 * Endpoints disponibles:
 * - GET /api/telemetria/latest - Últimas posiciones
 * - GET /api/telemetria/{deviceId} - Posición actual de un dispositivo
 * - GET /api/telemetria/{deviceId}/history - Historial de un dispositivo
 * - GET /api/telemetria/{deviceId}/route - Ruta reciente de un dispositivo
 * - GET /api/telemetria/realtime - Datos en tiempo real (últimos 3 min)
 */
class TelemetryController extends BaseApiController
{
    /**
     * Obtener ruta reciente de un dispositivo
     * 
     * @param string $deviceId ID del dispositivo
     * @param Request $request limit (default: 20)
     */
    public function getRoute(string $deviceId, Request $request): JsonResponse
    {
        $limit = min((int) $request->input('limit', 20), 100);
        
        $route = Telemetria::where('device_id', $deviceId)
            ->orderByDesc('created_at')
            ->take($limit)
            ->get(['id', 'device_id', 'lat', 'lon', 'battery', 'speed', 'created_at'])
            ->reverse()
            ->values();
        
        if ($route->isEmpty()) {
            return $this->errorResponse(
                "No hay datos de ruta para el dispositivo '{$deviceId}'",
                404
            );
        }
        
        return $this->successResponse([
            'device_id' => $deviceId,
            'points' => $route,
            'count' => $route->count()
        ], "Ruta del dispositivo '{$deviceId}' obtenida correctamente");
    }
    
    /**
     * Obtener últimas posiciones de todos los dispositivos
     * 
     * @deprecated Usar DeviceController::index() en su lugar
     */
    public function latestPositions(): JsonResponse
    {
        $latest = Telemetria::latestByDevice()->get();
        
        return $this->successResponse([
            'devices' => $latest,
            'count' => $latest->count()
        ], 'Últimas posiciones obtenidas correctamente');
    }

    /**
     * Últimos registros por dispositivo con cache
     * Usa Redis Cache con TTL dinámico
     */
    public function latest(): JsonResponse
    {
        // Última actualización en la BD
        $lastUpdate = Telemetria::latest('updated_at')->value('updated_at');
        $secondsSinceUpdate = now()->diffInSeconds($lastUpdate ?? now());

        // TTL adaptativo
        $ttl = $secondsSinceUpdate < 5 ? 2 : 5;
        $cacheKey = "telemetria_latest_ttl_{$ttl}";

        // Verificar si ya está cacheado
        $wasCached = Cache::has($cacheKey);

        // Obtener o guardar datos en cache Redis
        $data = Cache::remember($cacheKey, $ttl, function () {
            return Telemetria::latestByDevice()->get();
        });

        Log::info('Cache Telemetria', [
            'key' => $cacheKey,
            'ttl' => "{$ttl}s",
            'cached' => $wasCached ? 'HIT' : 'MISS',
            'records' => $data->count(),
        ]);

        return $this->successResponse([
            'cached' => $wasCached ? 'HIT' : 'MISS',
            'ttl' => "{$ttl}s",
            'devices' => $data,
            'count' => $data->count()
        ], 'Últimas posiciones obtenidas correctamente');
    }

    /**
     * Mostrar última posición de un dispositivo
     */
    public function show(string $deviceId): JsonResponse
    {
        $data = Telemetria::where('device_id', $deviceId)
            ->orderByDesc('created_at')
            ->first();

        if (!$data) {
            return $this->errorResponse(
                "No hay registros para el dispositivo '{$deviceId}'",
                404
            );
        }

        return $this->successResponse(
            $data,
            "Telemetría del dispositivo '{$deviceId}' obtenida correctamente"
        );
    }

    /**
     * Historial de un dispositivo
     */
    public function history(string $deviceId, Request $request): JsonResponse
    {
        $limit = min((int) $request->input('limit', 50), 200);

        $data = Telemetria::where('device_id', $deviceId)
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();

        return $this->successResponse([
            'device_id' => $deviceId,
            'history' => $data,
            'count' => $data->count()
        ], "Historial del dispositivo '{$deviceId}' obtenido correctamente");
    }

    /**
     * Datos en tiempo real (últimos 3 minutos)
     */
    public function realtime(): JsonResponse
    {
        $data = Telemetria::where('created_at', '>=', now()->subMinutes(3))
            ->orderByDesc('created_at')
            ->get();

        return $this->successResponse([
            'devices' => $data,
            'count' => $data->count(),
            'time_window' => '3 minutes'
        ], 'Datos en tiempo real obtenidos correctamente');
    }
}
