<?php

namespace App\Http\Controllers\Api;

use App\Models\Telemetria;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para estadísticas del sistema
 * 
 * Proporciona métricas y estadísticas agregadas sobre:
 * - Dispositivos (vehículos y conductores)
 * - Baterías
 * - Mantenimiento
 * - Distribución por sedes
 */
class StatsController extends BaseApiController
{
    /**
     * Obtener estadísticas generales del sistema
     */
    public function general(): JsonResponse
    {
        $latest = Telemetria::latestByDevice()->get();
        
        $stats = [
            'total_dispositivos' => $latest->count(),
            
            'vehiculos' => [
                'total' => $latest->where('device_type', 'vehiculo')->count(),
                'activos' => $latest->where('device_type', 'vehiculo')
                    ->where('status', 'active')->count(),
                'en_espera' => $latest->where('device_type', 'vehiculo')
                    ->where('status', 'idle')->count(),
                'cargando' => $latest->where('device_type', 'vehiculo')
                    ->where('status', 'charging')->count(),
                'mantenimiento' => $latest->where('device_type', 'vehiculo')
                    ->where('status', 'maintenance')->count(),
                'offline' => $latest->where('device_type', 'vehiculo')
                    ->where('status', 'offline')->count(),
            ],
            
            'conductores' => [
                'total' => $latest->where('device_type', 'conductor')->count(),
                'activos' => $latest->where('device_type', 'conductor')
                    ->where('status', 'active')->count(),
                'en_espera' => $latest->where('device_type', 'conductor')
                    ->where('status', 'idle')->count(),
                'offline' => $latest->where('device_type', 'conductor')
                    ->where('status', 'offline')->count(),
            ],
            
            'bateria' => [
                'promedio' => round($latest->avg('battery'), 1),
                'critica' => $latest->where('battery', '<', 20)->count(),
                'baja' => $latest->whereBetween('battery', [20, 40])->count(),
                'normal' => $latest->where('battery', '>=', 40)->count(),
                'maxima' => $latest->max('battery'),
                'minima' => $latest->min('battery'),
            ],
            
            'mantenimiento' => [
                'requerido' => $latest->filter(fn($d) => $d->needsMaintenance())->count(),
                'urgente' => $latest->where('maintenance_km_left', '<=', 50)->count(),
                'proximo' => $latest->whereBetween('maintenance_km_left', [51, 100])->count(),
            ],
            
            'velocidad' => [
                'promedio' => round($latest->avg('speed'), 1),
                'maxima' => $latest->max('speed'),
            ],
            
            'timestamp' => now()->toIso8601String(),
        ];
        
        return $this->successResponse($stats, 'Estadísticas generales obtenidas correctamente');
    }

    /**
     * Obtener estadísticas por sedes
     */
    public function branches(): JsonResponse
    {
        $branches = Branch::all();
        $latest = Telemetria::latestByDevice()->get();
        
        $branchStats = $branches->map(function ($branch) use ($latest) {
            $devicesInBranch = $latest->where('current_branch', $branch->nombre);
            $vehiclesInBranch = $devicesInBranch->where('device_type', 'vehiculo');
            
            return [
                'id' => $branch->id,
                'nombre' => $branch->nombre,
                'ubicacion' => [
                    'lat' => $branch->latitud,
                    'lng' => $branch->longitud,
                    'direccion' => $branch->direccion,
                ],
                'dispositivos' => [
                    'total' => $devicesInBranch->count(),
                    'vehiculos' => $vehiclesInBranch->count(),
                    'conductores' => $devicesInBranch->where('device_type', 'conductor')->count(),
                ],
                'vehiculos_por_estado' => [
                    'activos' => $vehiclesInBranch->where('status', 'active')->count(),
                    'en_espera' => $vehiclesInBranch->where('status', 'idle')->count(),
                    'cargando' => $vehiclesInBranch->where('status', 'charging')->count(),
                    'mantenimiento' => $vehiclesInBranch->where('status', 'maintenance')->count(),
                ],
                'capacidad' => [
                    'maxima' => $branch->capacidad_vehiculos,
                    'actual' => $vehiclesInBranch->count(),
                    'disponible' => max(0, $branch->capacidad_vehiculos - $vehiclesInBranch->count()),
                    'ocupacion_porcentaje' => $branch->capacidad_vehiculos > 0 
                        ? round(($vehiclesInBranch->count() / $branch->capacidad_vehiculos) * 100, 1)
                        : 0,
                ],
                'bateria' => [
                    'promedio' => round($devicesInBranch->avg('battery'), 1),
                    'critica' => $devicesInBranch->where('battery', '<', 20)->count(),
                ],
                'mantenimiento' => [
                    'requerido' => $devicesInBranch->filter(fn($d) => $d->needsMaintenance())->count(),
                ],
            ];
        });
        
        return $this->successResponse([
            'branches' => $branchStats,
            'total_branches' => $branches->count(),
        ], 'Estadísticas por sedes obtenidas correctamente');
    }

    /**
     * Obtener estadísticas de batería detalladas
     */
    public function battery(): JsonResponse
    {
        $latest = Telemetria::latestByDevice()->get();
        
        $batteryStats = [
            'general' => [
                'promedio' => round($latest->avg('battery'), 1),
                'maxima' => $latest->max('battery'),
                'minima' => $latest->min('battery'),
            ],
            'distribucion' => [
                'critica' => [
                    'count' => $latest->where('battery', '<', 20)->count(),
                    'porcentaje' => round(($latest->where('battery', '<', 20)->count() / max($latest->count(), 1)) * 100, 1),
                ],
                'baja' => [
                    'count' => $latest->whereBetween('battery', [20, 40])->count(),
                    'porcentaje' => round(($latest->whereBetween('battery', [20, 40])->count() / max($latest->count(), 1)) * 100, 1),
                ],
                'media' => [
                    'count' => $latest->whereBetween('battery', [41, 70])->count(),
                    'porcentaje' => round(($latest->whereBetween('battery', [41, 70])->count() / max($latest->count(), 1)) * 100, 1),
                ],
                'alta' => [
                    'count' => $latest->where('battery', '>', 70)->count(),
                    'porcentaje' => round(($latest->where('battery', '>', 70)->count() / max($latest->count(), 1)) * 100, 1),
                ],
            ],
            'salud_bateria' => [
                'promedio' => round($latest->avg('battery_health'), 1),
                'degradada' => $latest->where('battery_health', '<=', 70)->count(),
                'buena' => $latest->where('battery_health', '>', 70)->count(),
            ],
            'dispositivos_criticos' => $latest->where('battery', '<', 20)
                ->map(fn($d) => [
                    'device_id' => $d->device_id,
                    'battery' => $d->battery,
                    'status' => $d->status,
                    'branch' => $d->current_branch,
                ])
                ->values(),
        ];
        
        return $this->successResponse($batteryStats, 'Estadísticas de batería obtenidas correctamente');
    }

    /**
     * Obtener estadísticas de mantenimiento
     */
    public function maintenance(): JsonResponse
    {
        $latest = Telemetria::latestByDevice()->get();
        
        $maintenanceStats = [
            'resumen' => [
                'total_requieren' => $latest->filter(fn($d) => $d->needsMaintenance())->count(),
                'urgente' => $latest->where('maintenance_km_left', '<=', 50)->count(),
                'proximo' => $latest->whereBetween('maintenance_km_left', [51, 100])->count(),
                'ok' => $latest->where('maintenance_km_left', '>', 100)->count(),
            ],
            'por_razon' => [
                'kilometraje' => $latest->where('maintenance_km_left', '<=', 100)->count(),
                'bateria_degradada' => $latest->where('battery_health', '<=', 70)->count(),
            ],
            'dispositivos_urgentes' => $latest->where('maintenance_km_left', '<=', 50)
                ->map(fn($d) => [
                    'device_id' => $d->device_id,
                    'km_restantes' => $d->maintenance_km_left,
                    'battery_health' => $d->battery_health,
                    'odometer' => $d->odometer,
                    'branch' => $d->current_branch,
                ])
                ->values(),
            'promedio_km_restantes' => round($latest->avg('maintenance_km_left'), 0),
        ];
        
        return $this->successResponse($maintenanceStats, 'Estadísticas de mantenimiento obtenidas correctamente');
    }

    /**
     * Obtener resumen ejecutivo (dashboard)
     */
    public function dashboard(): JsonResponse
    {
        $latest = Telemetria::latestByDevice()->get();
        
        $dashboard = [
            'dispositivos' => [
                'total' => $latest->count(),
                'activos' => $latest->where('status', 'active')->count(),
                'offline' => $latest->where('status', 'offline')->count(),
            ],
            'alertas' => [
                'bateria_critica' => $latest->where('battery', '<', 20)->count(),
                'mantenimiento_urgente' => $latest->where('maintenance_km_left', '<=', 50)->count(),
                'offline' => $latest->where('status', 'offline')->count(),
            ],
            'metricas' => [
                'bateria_promedio' => round($latest->avg('battery'), 1),
                'velocidad_promedio' => round($latest->where('status', 'active')->avg('speed'), 1),
                'dispositivos_en_movimiento' => $latest->where('speed', '>', 0)->count(),
            ],
            'timestamp' => now()->toIso8601String(),
        ];
        
        return $this->successResponse($dashboard, 'Dashboard obtenido correctamente');
    }
}
