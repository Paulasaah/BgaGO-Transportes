<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Telemetria;

// Controladores API existentes (Telemetría IoT)
use App\Http\Controllers\Api\IotDataController;
use App\Http\Controllers\Api\AzureTelemetryController;
use App\Http\Controllers\Api\AzureDeviceController;
use App\Http\Controllers\Api\TelemetryController;

// Nuevos Controladores (Sistema de Reservas y Domicilios)
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\BranchController;

/*
|--------------------------------------------------------------------------
| API Routes - BgaGO
|--------------------------------------------------------------------------
| Sistema integrado de:
| - Telemetría IoT (MQTT) para tracking de vehículos
| - Gestión de reservas y domicilios
| - Procesamiento de pagos
| - Integración con Azure IoT Central
*/

// ============================================================================
// 🔵 AZURE IOT CENTRAL
// ============================================================================

Route::prefix('azure')->group(function () {
    Route::put('/device/{deviceId}', [AzureDeviceController::class, 'registerDevice']);
    Route::get('/telemetria/{deviceId}', [AzureTelemetryController::class, 'getDeviceTelemetry']);
});

// ============================================================================
// 🟢 ESTADO DEL API
// ============================================================================

Route::get('/status', function () {
    return response()->json([
        'status' => 'online',
        'message' => 'BgaGO API funcionando correctamente 🚀',
        'timestamp' => now()->toIso8601String(),
        'version' => '2.0',
        'services' => [
            'telemetria' => 'active',
            'reservas' => 'active',
            'domicilios' => 'active',
            'pagos' => 'active',
        ]
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

Route::prefix('telemetria')->group(function () {
    // Endpoint principal para telemetría completa
    Route::post('/', [TelemetryController::class, 'store']);
    Route::get('/realtime', [TelemetryController::class, 'realtime']);
    
    // Endpoint legacy (formato simple)
    Route::post('/simple', function (Request $request) {
        try {
            $data = $request->all();
            
            Log::info('📡 Telemetría simple recibida', [
                'device_id' => $data['device_id'] ?? 'unknown'
            ]);
            
            $lat = $data['lat'] ?? $data['Geopoint']['lat'] ?? 0;
            $lon = $data['lon'] ?? $data['Geopoint']['lon'] ?? 0;
            $alt = $data['alt'] ?? $data['Geopoint']['alt'] ?? null;
            $battery = $data['battery'] ?? $data['Battery'] ?? 100;
            $deviceId = $data['device_id'] ?? 'desconocido';
            
            $telemetria = Telemetria::create([
                'device_id' => $deviceId,
                'device_type' => 'vehiculo',
                'status' => 'active',
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
            Log::error('❌ Error en telemetría simple', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar telemetría',
                'error' => $e->getMessage()
            ], 500);
        }
    });
    
    // Consultas de telemetría
    Route::get('/latest', [TelemetryController::class, 'latest']);
    Route::get('/{deviceId}', [TelemetryController::class, 'show']);
    Route::get('/{deviceId}/history', [TelemetryController::class, 'history']);
    
    // Debug
    Route::get('/all/debug', function (Request $request) {
        $perPage = $request->input('per_page', 50);
        $telemetry = Telemetria::orderByDesc('id')->paginate($perPage);
        
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
    
    // Cleanup (protegido)
    Route::delete('/cleanup', function (Request $request) {
        $days = $request->input('days', 7);
        $deleted = Telemetria::where('created_at', '<', now()->subDays($days))->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Registros eliminados correctamente",
            'deleted_count' => $deleted,
            'older_than_days' => $days
        ]);
    })->middleware('auth:sanctum');
});

// ============================================================================
// 🗺️ RUTAS Y TRACKING
// ============================================================================

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

Route::get('/ultimas', function () {
    try {
        $subquery = DB::table('telemetrias')
            ->select('device_id', DB::raw('MAX(created_at) as last_time'))
            ->groupBy('device_id');
        
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
// 🔍 FILTROS Y BÚSQUEDAS DE DISPOSITIVOS
// ============================================================================

Route::prefix('dispositivos')->group(function () {
    Route::get('/tipo/{tipo}', function ($tipo) {
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
    
    Route::get('/estado/{estado}', function ($estado) {
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
    
    Route::get('/sede/{sede}', function ($sede) {
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
    
    Route::get('/mantenimiento', function () {
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
});

// ============================================================================
// 📈 ESTADÍSTICAS
// ============================================================================

Route::prefix('estadisticas')->group(function () {
    Route::get('/', function () {
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
    
    Route::get('/sedes', function () {
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
});

// ============================================================================
// 🚗 VEHÍCULOS - CATÁLOGO Y DISPONIBILIDAD
// ============================================================================

Route::prefix('vehicles')->group(function () {
    // Catálogo público
    Route::get('/', [VehicleController::class, 'index']);
    Route::get('/{vehicle}', [VehicleController::class, 'show']);
    
    // Disponibilidad
    Route::post('/{vehicle}/check-availability', [VehicleController::class, 'checkAvailability']);
    Route::get('/{vehicle}/schedule', [VehicleController::class, 'schedule']);
    Route::get('/{vehicle}/alternatives', [VehicleController::class, 'alternatives']);
    
    // Precios
    Route::post('/compare-prices', [VehicleController::class, 'comparePrices']);
    Route::post('/quick-estimate', [VehicleController::class, 'quickEstimate']);
    
    // Filtros
    Route::get('/tipo/{tipo}', [VehicleController::class, 'byType']);
    Route::get('/sede/{sedeId}', [VehicleController::class, 'byBranch']);
    
    // Tracking
    Route::get('/{vehicle}/location', [VehicleController::class, 'location']);
    
    // Stats (admin)
    Route::get('/{vehicle}/stats', [VehicleController::class, 'stats'])
        ->middleware('auth:sanctum');
});

// ============================================================================
// 🏢 SEDES - UBICACIONES Y COBERTURA
// ============================================================================

Route::prefix('branches')->group(function () {
    Route::get('/', [BranchController::class, 'index']);
    Route::get('/{branch}', [BranchController::class, 'show']);
    Route::get('/{branch}/vehicles', [BranchController::class, 'vehicles']);
    Route::get('/{branch}/stats', [BranchController::class, 'stats']);
    
    // Búsqueda geográfica
    Route::post('/nearest', [BranchController::class, 'nearest']);
    Route::post('/in-radius', [BranchController::class, 'inRadius']);
});

// ============================================================================
// 📅 RESERVAS - REQUIERE AUTENTICACIÓN
// ============================================================================

Route::middleware('auth:sanctum')->prefix('reservations')->group(function () {
    // CRUD básico (admin)
    Route::get('/', [ReservationController::class, 'index'])
        ->middleware('can:viewAny,App\Models\Reservation');
    Route::post('/', [ReservationController::class, 'store']);
    Route::get('/{reservation}', [ReservationController::class, 'show']);
    
    // Acciones sobre reservas
    Route::post('/{reservation}/confirm', [ReservationController::class, 'confirm']);
    Route::post('/{reservation}/start', [ReservationController::class, 'start']);
    Route::post('/{reservation}/complete', [ReservationController::class, 'complete']);
    Route::post('/{reservation}/cancel', [ReservationController::class, 'cancel']);
    Route::post('/{reservation}/rate', [ReservationController::class, 'rate']);
    
    // Mis reservas
    Route::get('/me/list', [ReservationController::class, 'myReservations']);
    Route::get('/me/stats', [ReservationController::class, 'myStats']);
});

// ============================================================================
// 📦 DOMICILIOS - REQUIERE AUTENTICACIÓN
// ============================================================================

Route::middleware('auth:sanctum')->prefix('deliveries')->group(function () {
    // CRUD
    Route::get('/', [DeliveryController::class, 'index'])
        ->middleware('role:admin');
    Route::post('/package', [DeliveryController::class, 'storePackage']);
    Route::post('/vehicle', [DeliveryController::class, 'storeVehicle']);
    Route::get('/{delivery}', [DeliveryController::class, 'show']);
    
    // Acciones
    Route::post('/{delivery}/assign-driver', [DeliveryController::class, 'assignDriver'])
        ->middleware('role:admin|dispatcher');
    Route::post('/{delivery}/start', [DeliveryController::class, 'start']);
    Route::post('/{delivery}/complete', [DeliveryController::class, 'complete']);
    
    // Tracking
    Route::get('/{delivery}/track', [DeliveryController::class, 'track']);
    
    // Mis domicilios
    Route::get('/me/list', [DeliveryController::class, 'myDeliveries']);
    
    // Para conductores
    Route::get('/me/assigned', [DeliveryController::class, 'myAssignedDeliveries'])
        ->middleware('role:conductor');
    
    // Para admin/dispatcher
    Route::get('/pending/list', [DeliveryController::class, 'pending'])
        ->middleware('role:admin|dispatcher');
});

// ============================================================================
// 💳 PAGOS - REQUIERE AUTENTICACIÓN
// ============================================================================

Route::middleware('auth:sanctum')->prefix('payments')->group(function () {
    // Crear intención de pago
    Route::post('/reservations/{reservation}/create-intent', [PaymentController::class, 'createPaymentIntent']);
    
    // Procesar pago
    Route::post('/{payment}/process', [PaymentController::class, 'processPayment']);
    
    // Acciones de admin
    Route::post('/{payment}/approve', [PaymentController::class, 'approve'])
        ->middleware('can:manage-payments');
    Route::post('/{payment}/reject', [PaymentController::class, 'reject'])
        ->middleware('can:manage-payments');
    Route::post('/{payment}/refund', [PaymentController::class, 'refund'])
        ->middleware('can:manage-payments');
    
    // Consultas
    Route::get('/{payment}', [PaymentController::class, 'show']);
    Route::get('/me/list', [PaymentController::class, 'myPayments']);
    
    // Lista completa (admin)
    Route::get('/', [PaymentController::class, 'index'])
        ->middleware('can:manage-payments');
    
    // Métodos disponibles
    Route::get('/methods/available', [PaymentController::class, 'paymentMethods']);
});

// ============================================================================
// 🚫 RUTA 404 PERSONALIZADA
// ============================================================================

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint no encontrado',
        'documentation' => [
            'telemetria' => '/api/telemetria/*',
            'vehiculos' => '/api/vehicles/*',
            'sedes' => '/api/branches/*',
            'reservas' => '/api/reservations/* (auth)',
            'domicilios' => '/api/deliveries/* (auth)',
            'pagos' => '/api/payments/* (auth)',
        ]
    ], 404);
});