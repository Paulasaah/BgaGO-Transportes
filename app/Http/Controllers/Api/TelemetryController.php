<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Telemetria;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TelemetryController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            
            Log::info('📡 Telemetría recibida', [
                'device_id' => $data['device_id'] ?? 'unknown',
                'type' => $data['device_type'] ?? 'unknown',
                'status' => $data['status'] ?? 'unknown'
            ]);

            // Validación
            $validator = Validator::make($data, [
                'device_id' => 'required|string',
                'device_type' => 'required|in:vehiculo,conductor',
                'status' => 'required|in:active,idle,charging,maintenance,offline',
                'lat' => 'required|numeric',
                'lon' => 'required|numeric',
                'battery' => 'required|numeric|min:0|max:100',
            ]);

            if ($validator->fails()) {
                Log::warning('⚠️ Validación fallida', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Crear registro
            $telemetry = Telemetria::create([
                'device_id' => $data['device_id'],
                'device_type' => $data['device_type'],
                'status' => $data['status'],
                'lat' => $data['lat'],
                'lon' => $data['lon'],
                'alt' => $data['alt'] ?? null,
                'battery' => $data['battery'],
                'battery_health' => $data['battery_health'] ?? 100,
                'speed' => $data['speed'] ?? 0,
                'current_branch' => $data['current_branch'] ?? null,
                'target_branch' => $data['target_branch'] ?? null,
                'odometer' => $data['odometer'] ?? 0,
                'trip_count' => $data['trip_count'] ?? 0,
                'maintenance_km_left' => $data['maintenance_km_left'] ?? 1000,
                'last_maintenance' => $data['last_maintenance'] ?? null,
                'driver_name' => $data['driver_name'] ?? null,
                'deliveries_completed' => $data['deliveries_completed'] ?? 0,
                'rating' => $data['rating'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Telemetría guardada correctamente',
                'data' => [
                    'id' => $telemetry->id,
                    'device_id' => $telemetry->device_id,
                ]
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
     * Obtener última telemetría de un dispositivo
     */
    public function show($deviceId)
    {
        $telemetry = Telemetria::where('device_id', $deviceId)
            ->orderByDesc('id')
            ->first();

        if (!$telemetry) {
            return response()->json([
                'success' => false,
                'message' => 'Dispositivo no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $telemetry
        ]);
    }

    /**
     * Obtener historial de un dispositivo
     */
    public function history($deviceId, Request $request)
    {
        $limit = $request->input('limit', 50);

        $history = Telemetria::where('device_id', $deviceId)
            ->orderByDesc('id')
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history,
            'count' => $history->count()
        ]);
    }

    /**
     * Obtener todas las últimas posiciones
     */
    public function latest()
    {
        $latest = Telemetria::latestByDevice()->get();

        return response()->json([
            'success' => true,
            'data' => $latest,
            'count' => $latest->count()
        ]);
    }
}