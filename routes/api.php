<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Telemetria;

// Controladores API
use App\Http\Controllers\Api\IotDataController;
use App\Http\Controllers\Api\AzureTelemetryController;
use App\Http\Controllers\Api\AzureDeviceController;
use App\Http\Controllers\Api\TelemetryController;


/*
|--------------------------------------------------------------------------
| API Routes - BgaGO
|--------------------------------------------------------------------------
| Rutas para gestión de telemetría IoT, integración con Azure IoT Central
| y monitoreo en tiempo real de vehículos y conductores.
*/

// ============================================================================
// 🔵 AZURE IOT CENTRAL
// ============================================================================

// Registrar dispositivo en Azure
Route::put('/azure/device/{deviceId}', [AzureDeviceController::class, 'registerDevice']);

// Consultar telemetría desde Azure IoT Central
Route::get('/azure/telemetria/{deviceId}', [AzureTelemetryController::class, 'getDeviceTelemetry']);

// ============================================================================
// 🟢 ESTADO DEL API
// ============================================================================

// Verificar que el API está funcionando
Route::get('/status', function () {
    return response()->json([
        'status' => 'online',
        'message' => 'API funcionando correctamente 🚀',
        'timestamp' => now()->toIso8601String(),
        'version' => '2.0'
    ]);
});

Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'Pong! API está activa',
        'server_time' => now()->toIso8601String()
    ]);
});

// ============================================================================
// 📡 RECEPCIÓN DE TELEMETRÍA (MQTT → Laravel)
// ============================================================================

/**
 * Endpoint principal para recibir telemetría desde el subscriber MQTT
 * Soporta formato mejorado con todos los campos
 */
Route::post('/telemetria', [TelemetryController::class, 'store']);
Route::get('/telemetria/realtime', [TelemetryController::class, 'realtime']);

/**
 * Endpoint legacy (mantener compatibilidad con versión anterior)
 * Soporta formato simple: {device_id, Geopoint: {lat, lon}, Battery}
 */
Route::post('/telemetria/simple', function (Request $request) {
    try {
        $data = $request->all();
        
        Log::info('📡 Telemetría simple recibida', [
            'device_id' => $data['device_id'] ?? 'unknown'
        ]);
        
        // Extraer coordenadas (soporta ambos formatos)
        $lat = $data['lat'] ?? $data['Geopoint']['lat'] ?? 0;
        $lon = $data['lon'] ?? $data['Geopoint']['lon'] ?? 0;
        $alt = $data['alt'] ?? $data['Geopoint']['alt'] ?? null;
        $battery = $data['battery'] ?? $data['Battery'] ?? 100;
        $deviceId = $data['device_id'] ?? 'desconocido';
        
        // Crear registro con campos mínimos
        $telemetria = Telemetria::create([
            'device_id' => $deviceId,
            'device_type' => 'vehiculo', // Default
            'status' => 'active', // Default
            'lat' => (float) $lat,
            'lon' => (float) $lon,
            'alt' => $alt ? (float) $alt : null,
            'battery' => (float) $battery,
        ]);
        
        Log::info('✅ Telemetría simple guardada', ['id' => $telemetria->id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Telemetría guardada',
            'data' => $telemetria
        ], 201);
        
    } catch (\Throwable $e) {
        Log::error('❌ Error en telemetría simple', [
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error al guardar telemetría',
            'error' => $e->getMessage()
        ], 500);
    }
});

// ============================================================================
// 📊 CONSULTAS DE TELEMETRÍA
// ============================================================================

/**
 * Obtener últimas posiciones de TODOS los dispositivos
 * Usado por el mapa para mostrar posición actual
 */
Route::get('/telemetria/latest', [TelemetryController::class, 'latest']);

/**
 * Obtener última posición de un dispositivo específico
 * GET /api/telemetria/{device_id}
 */
Route::get('/telemetria/{deviceId}', [TelemetryController::class, 'show']);

/**
 * Obtener historial completo de un dispositivo
 * GET /api/telemetria/{device_id}/history?limit=50
 */
Route::get('/telemetria/{deviceId}/history', [TelemetryController::class, 'history']);

/**
 * Ruta para obtener la ruta reciente (últimos 20 puntos)
 * Usado para dibujar el path en el mapa
 */
Route::get('/ruta/{device_id}', function ($device_id) {
    try {
        $route = Telemetria::where('device_id', $device_id)
            ->orderByDesc('id')
            ->take(20)
            ->get(['id', 'device_id', 'lat', 'lon', 'battery', 'speed', 'created_at'])
            ->reverse()
            ->values();
        
        return response()->json([
            'success' => true,
            'device_id' => $device_id,
            'points' => $route,
            'count' => $route->count()
        ]);
        
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener ruta',
            'error' => $e->getMessage()
        ], 500);
    }
});

/**
 * Obtener últimas posiciones (formato legacy)
 * Mantener compatibilidad con versión anterior del mapa
 */
Route::get('/ultimas', function () {
    try {
        // Subconsulta para obtener el último timestamp de cada dispositivo
        $subquery = DB::table('telemetrias')
            ->select('device_id', DB::raw('MAX(created_at) as last_time'))
            ->groupBy('device_id');
        
        // Join para obtener el registro completo
        $latest = DB::table('telemetrias')
            ->joinSub($subquery, 't2', function ($join) {
                $join->on('telemetrias.device_id', '=', 't2.device_id')
                     ->on('telemetrias.created_at', '=', 't2.last_time');
            })
            ->select('telemetrias.*')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $latest,
            'count' => $latest->count()
        ]);
        
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener últimas posiciones',
            'error' => $e->getMessage()
        ], 500);
    }
});

// ============================================================================
// 🔍 FILTROS Y BÚSQUEDAS AVANZADAS
// ============================================================================

/**
 * Obtener dispositivos por tipo
 * GET /api/dispositivos/tipo/{tipo}
 * Tipos: vehiculo, conductor
 */
Route::get('/dispositivos/tipo/{tipo}', function ($tipo) {
    if (!in_array($tipo, ['vehiculo', 'conductor'])) {
        return response()->json([
            'success' => false,
            'message' => 'Tipo inválido. Use: vehiculo o conductor'
        ], 400);
    }
    
    $devices = Telemetria::latestByDevice()
        ->where('device_type', $tipo)
        ->get();
    
    return response()->json([
        'success' => true,
        'tipo' => $tipo,
        'data' => $devices,
        'count' => $devices->count()
    ]);
});

/**
 * Obtener dispositivos por estado
 * GET /api/dispositivos/estado/{estado}
 * Estados: active, idle, charging, maintenance, offline
 */
Route::get('/dispositivos/estado/{estado}', function ($estado) {
    $validStatuses = ['active', 'idle', 'charging', 'maintenance', 'offline'];
    
    if (!in_array($estado, $validStatuses)) {
        return response()->json([
            'success' => false,
            'message' => 'Estado inválido',
            'valid_states' => $validStatuses
        ], 400);
    }
    
    $devices = Telemetria::latestByDevice()
        ->where('status', $estado)
        ->get();
    
    return response()->json([
        'success' => true,
        'estado' => $estado,
        'data' => $devices,
        'count' => $devices->count()
    ]);
});

/**
 * Obtener dispositivos en una sede específica
 * GET /api/dispositivos/sede/{sede}
 */
Route::get('/dispositivos/sede/{sede}', function ($sede) {
    $devices = Telemetria::latestByDevice()
        ->where('current_branch', $sede)
        ->get();
    
    return response()->json([
        'success' => true,
        'sede' => $sede,
        'data' => $devices,
        'count' => $devices->count()
    ]);
});

/**
 * Obtener dispositivos que necesitan mantenimiento
 * GET /api/dispositivos/mantenimiento
 */
Route::get('/dispositivos/mantenimiento', function () {
    $devices = Telemetria::latestByDevice()
        ->where(function ($query) {
            $query->where('maintenance_km_left', '<=', 100)
                  ->orWhere('battery_health', '<=', 70);
        })
        ->get();
    
    return response()->json([
        'success' => true,
        'message' => 'Dispositivos que requieren mantenimiento',
        'data' => $devices,
        'count' => $devices->count()
    ]);
});

// ============================================================================
// 📈 ESTADÍSTICAS
// ============================================================================

/**
 * Obtener estadísticas generales del sistema
 * GET /api/estadisticas
 */
Route::get('/estadisticas', function () {
    $latest = Telemetria::latestByDevice()->get();
    
    $stats = [
        'total_dispositivos' => $latest->count(),
        'vehiculos' => [
            'total' => $latest->where('device_type', 'vehiculo')->count(),
            'activos' => $latest->where('device_type', 'vehiculo')->where('status', 'active')->count(),
            'en_espera' => $latest->where('device_type', 'vehiculo')->where('status', 'idle')->count(),
            'cargando' => $latest->where('device_type', 'vehiculo')->where('status', 'charging')->count(),
            'mantenimiento' => $latest->where('device_type', 'vehiculo')->where('status', 'maintenance')->count(),
        ],
        'conductores' => [
            'total' => $latest->where('device_type', 'conductor')->count(),
            'activos' => $latest->where('device_type', 'conductor')->where('status', 'active')->count(),
            'en_espera' => $latest->where('device_type', 'conductor')->where('status', 'idle')->count(),
        ],
        'bateria' => [
            'promedio' => round($latest->avg('battery'), 1),
            'critica' => $latest->where('battery', '<', 20)->count(),
            'baja' => $latest->whereBetween('battery', [20, 40])->count(),
            'normal' => $latest->where('battery', '>=', 40)->count(),
        ],
        'mantenimiento' => [
            'requerido' => $latest->filter(fn($d) => $d->needsMaintenance())->count(),
        ],
        'timestamp' => now()->toIso8601String(),
    ];
    
    return response()->json([
        'success' => true,
        'data' => $stats
    ]);
});

/**
 * Obtener estadísticas por sede
 * GET /api/estadisticas/sedes
 */
Route::get('/estadisticas/sedes', function () {
    $branches = \App\Models\Branch::all();
    $latest = Telemetria::latestByDevice()->get();
    
    $branchStats = $branches->map(function ($branch) use ($latest) {
        $devicesInBranch = $latest->where('current_branch', $branch->nombre);
        
        return [
            'nombre' => $branch->nombre,
            'total_dispositivos' => $devicesInBranch->count(),
            'vehiculos' => $devicesInBranch->where('device_type', 'vehiculo')->count(),
            'conductores' => $devicesInBranch->where('device_type', 'conductor')->count(),
            'capacidad' => $branch->capacidad_vehiculos,
            'ocupacion_porcentaje' => $branch->capacidad_vehiculos > 0 
                ? round(($devicesInBranch->count() / $branch->capacidad_vehiculos) * 100, 1)
                : 0,
            'bateria_promedio' => round($devicesInBranch->avg('battery'), 1),
        ];
    });
    
    return response()->json([
        'success' => true,
        'data' => $branchStats
    ]);
});

// ============================================================================
// 🧪 ENDPOINTS DE DESARROLLO/DEBUG
// ============================================================================

/**
 * Obtener todos los registros de telemetría (paginado)
 * Solo para desarrollo - NO usar en producción con muchos datos
 */
Route::get('/telemetria/all/debug', function (Request $request) {
    $perPage = $request->input('per_page', 50);
    
    $telemetry = Telemetria::orderByDesc('id')
        ->paginate($perPage);
    
    return response()->json([
        'success' => true,
        'data' => $telemetry->items(),
        'pagination' => [
            'total' => $telemetry->total(),
            'per_page' => $telemetry->perPage(),
            'current_page' => $telemetry->currentPage(),
            'last_page' => $telemetry->lastPage(),
        ]
    ]);
});

/**
 * Limpiar datos antiguos (solo para testing)
 * DELETE /api/telemetria/cleanup?days=7
 */
Route::delete('/telemetria/cleanup', function (Request $request) {
    $days = $request->input('days', 7);
    
    $deleted = Telemetria::where('created_at', '<', now()->subDays($days))->delete();
    
    return response()->json([
        'success' => true,
        'message' => "Registros eliminados correctamente",
        'deleted_count' => $deleted,
        'older_than_days' => $days
    ]);
})->middleware('auth:sanctum'); // Proteger con autenticación

// ============================================================================
// 🚫 RUTA 404 PERSONALIZADA PARA API
// ============================================================================

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint no encontrado',
        'available_endpoints' => [
            'GET /api/status - Estado del API',
            'POST /api/telemetria - Recibir telemetría',
            'GET /api/telemetria/latest - Últimas posiciones',
            'GET /api/telemetria/{id} - Telemetría específica',
            'GET /api/ruta/{device_id} - Ruta de dispositivo',
            'GET /api/estadisticas - Estadísticas generales',
            'GET /api/dispositivos/tipo/{tipo} - Filtrar por tipo',
            'GET /api/dispositivos/estado/{estado} - Filtrar por estado',
        ]
    ], 404);
});