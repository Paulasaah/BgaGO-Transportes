<?php

namespace App\Http\Controllers\Api;

use App\Models\Telemetria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controlador para gestión de dispositivos IoT
 * 
 * Maneja consultas y filtros de dispositivos (vehículos y conductores)
 * basados en la telemetría más reciente.
 */
class DeviceController extends BaseApiController
{
    /**
     * Obtener dispositivos por tipo
     * 
     * @param string $tipo vehiculo|conductor
     */
    public function byType(string $tipo): JsonResponse
    {
        $validTypes = ['vehiculo', 'conductor'];
        
        if (!in_array($tipo, $validTypes)) {
            return $this->errorResponse(
                'Tipo inválido. Use: vehiculo o conductor',
                400,
                ['valid_types' => $validTypes]
            );
        }
        
        $devices = Telemetria::latestByDevice()
            ->where('device_type', $tipo)
            ->get();
        
        return $this->successResponse([
            'tipo' => $tipo,
            'devices' => $devices,
            'count' => $devices->count()
        ], "Dispositivos tipo '{$tipo}' obtenidos correctamente");
    }

    /**
     * Obtener dispositivos por estado
     * 
     * @param string $estado active|idle|charging|maintenance|offline
     */
    public function byStatus(string $estado): JsonResponse
    {
        $validStatuses = ['active', 'idle', 'charging', 'maintenance', 'offline'];
        
        if (!in_array($estado, $validStatuses)) {
            return $this->errorResponse(
                'Estado inválido',
                400,
                ['valid_statuses' => $validStatuses]
            );
        }
        
        $devices = Telemetria::latestByDevice()
            ->where('status', $estado)
            ->get();
        
        return $this->successResponse([
            'estado' => $estado,
            'devices' => $devices,
            'count' => $devices->count()
        ], "Dispositivos con estado '{$estado}' obtenidos correctamente");
    }

    /**
     * Obtener dispositivos por sede
     * 
     * @param string $sede Nombre de la sede
     */
    public function byBranch(string $sede): JsonResponse
    {
        $devices = Telemetria::latestByDevice()
            ->where('current_branch', $sede)
            ->get();
        
        return $this->successResponse([
            'sede' => $sede,
            'devices' => $devices,
            'count' => $devices->count()
        ], "Dispositivos en sede '{$sede}' obtenidos correctamente");
    }

    /**
     * Obtener dispositivos que requieren mantenimiento
     * 
     * Criterios:
     * - Kilómetros restantes <= 100 km
     * - Salud de batería <= 70%
     */
    public function needsMaintenance(): JsonResponse
    {
        $devices = Telemetria::latestByDevice()
            ->where(function ($query) {
                $query->where('maintenance_km_left', '<=', 100)
                      ->orWhere('battery_health', '<=', 70);
            })
            ->get();
        
        // Agregar razón del mantenimiento
        $devicesWithReason = $devices->map(function ($device) {
            $reasons = [];
            
            if ($device->maintenance_km_left <= 100) {
                $reasons[] = "Mantenimiento próximo ({$device->maintenance_km_left} km restantes)";
            }
            
            if ($device->battery_health <= 70) {
                $reasons[] = "Batería degradada ({$device->battery_health}% salud)";
            }
            
            $device->maintenance_reasons = $reasons;
            return $device;
        });
        
        return $this->successResponse([
            'devices' => $devicesWithReason,
            'count' => $devicesWithReason->count()
        ], 'Dispositivos que requieren mantenimiento');
    }

    /**
     * Obtener todos los dispositivos con filtros opcionales
     */
    public function index(Request $request): JsonResponse
    {
        $query = Telemetria::latestByDevice();
        
        // Filtros opcionales
        if ($request->has('type')) {
            $query->where('device_type', $request->type);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('branch')) {
            $query->where('current_branch', $request->branch);
        }
        
        if ($request->has('battery_below')) {
            $query->where('battery', '<=', $request->battery_below);
        }
        
        $devices = $query->get();
        
        return $this->successResponse([
            'devices' => $devices,
            'count' => $devices->count(),
            'filters_applied' => $request->only(['type', 'status', 'branch', 'battery_below'])
        ], 'Dispositivos obtenidos correctamente');
    }

    /**
     * Obtener información detallada de un dispositivo específico
     */
    public function show(string $deviceId): JsonResponse
    {
        $device = Telemetria::where('device_id', $deviceId)
            ->orderByDesc('created_at')
            ->first();
        
        if (!$device) {
            return $this->errorResponse(
                "Dispositivo '{$deviceId}' no encontrado",
                404
            );
        }
        
        // Obtener historial reciente (últimas 24 horas)
        $recentHistory = Telemetria::where('device_id', $deviceId)
            ->where('created_at', '>=', now()->subDay())
            ->orderByDesc('created_at')
            ->limit(100)
            ->get(['lat', 'lon', 'battery', 'speed', 'created_at']);
        
        return $this->successResponse([
            'device' => $device,
            'recent_history' => $recentHistory,
            'history_count' => $recentHistory->count()
        ], "Información del dispositivo '{$deviceId}' obtenida correctamente");
    }
}
