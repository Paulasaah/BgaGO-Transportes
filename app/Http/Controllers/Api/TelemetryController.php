<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Telemetria;

class TelemetryController extends Controller
{
    /**
     * 📡 Guardar nueva telemetría (POST /api/telemetria)
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();

            Log::info('📡 Telemetría recibida', [
                'device_id' => $data['device_id'] ?? 'unknown'
            ]);

            $telemetria = Telemetria::create([
                'device_id' => $data['device_id'],
                'device_type' => $data['device_type'] ?? 'vehiculo',
                'status' => $data['status'] ?? 'active',
                'lat' => (float) $data['lat'],
                'lon' => (float) $data['lon'],
                'alt' => $data['alt'] ?? null,
                'battery' => (float) ($data['battery'] ?? 100),
                'battery_health' => (float) ($data['battery_health'] ?? 100),
                'speed' => (float) ($data['speed'] ?? 0),
                'current_branch' => $data['current_branch'] ?? null,
                'target_branch' => $data['target_branch'] ?? null,
                'odometer' => (float) ($data['odometer'] ?? 0),
                'trip_count' => (int) ($data['trip_count'] ?? 0),
                'maintenance_km_left' => (float) ($data['maintenance_km_left'] ?? 1000),
                'last_maintenance' => $data['last_maintenance'] ?? null,
                'driver_name' => $data['driver_name'] ?? null,
                'deliveries_completed' => (int) ($data['deliveries_completed'] ?? 0),
                'rating' => $data['rating'] ?? null,
            ]);

            // 🧹 Invalida cache tras nueva telemetría
            Cache::forget('telemetria_latest_ttl_2');
            Cache::forget('telemetria_latest_ttl_5');

            return response()->json([
                'success' => true,
                'message' => 'Telemetría guardada y cache invalidado',
                'data' => $telemetria
            ], 201);

        } catch (\Throwable $e) {
            Log::error('❌ Error guardando telemetría', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar telemetría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📊 Últimos registros por dispositivo (GET /api/telemetria/latest)
     * 💾 Usa Redis Cache con TTL dinámico y métricas
     */
    public function latest()
    {
        try {
            // 🔍 Última actualización en la BD
            $lastUpdate = Telemetria::latest('updated_at')->value('updated_at');
            $secondsSinceUpdate = now()->diffInSeconds($lastUpdate ?? now());

            // 🕒 TTL adaptativo
            $ttl = $secondsSinceUpdate < 5 ? 2 : 5;
            $cacheKey = "telemetria_latest_ttl_{$ttl}";

            // 🧠 Verificar si ya está cacheado
            $wasCached = Cache::has($cacheKey);

            // 💾 Obtener o guardar datos en cache Redis
            $data = Cache::remember($cacheKey, $ttl, function () {
                return Telemetria::latestByDevice()->get();
            });

            // 🧩 Log de rendimiento
            Log::info('🧠 Cache Telemetria', [
                'key' => $cacheKey,
                'ttl' => "{$ttl}s",
                'cached' => $wasCached ? 'HIT' : 'MISS',
                'records' => $data->count(),
            ]);

            return response()->json([
                'success' => true,
                'cached' => $wasCached ? 'HIT' : 'MISS',
                'ttl' => "{$ttl}s",
                'count' => $data->count(),
                'data' => $data
            ]);

        } catch (\Throwable $e) {
            Log::error('❌ Error en /api/telemetria/latest', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener telemetría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📍 Mostrar última posición de un dispositivo (GET /api/telemetria/{deviceId})
     */
    public function show($deviceId)
    {
        try {
            $data = Telemetria::where('device_id', $deviceId)
                ->orderByDesc('created_at')
                ->first();

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay registros para este dispositivo'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener telemetría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🕓 Historial de un dispositivo (GET /api/telemetria/{deviceId}/history)
     */
    public function history($deviceId, Request $request)
    {
        try {
            $limit = (int) $request->input('limit', 50);

            $data = Telemetria::where('device_id', $deviceId)
                ->orderByDesc('created_at')
                ->take($limit)
                ->get();

            return response()->json([
                'success' => true,
                'count' => $data->count(),
                'device_id' => $deviceId,
                'data' => $data
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener historial',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔄 Últimos datos en tiempo real (últimos 3 min)
     */
    public function realtime()
    {
        $data = Telemetria::where('created_at', '>=', now()->subMinutes(3))
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $data->count(),
            'data' => $data
        ]);
    }
}
