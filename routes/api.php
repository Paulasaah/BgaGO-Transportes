<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Telemetria;

// Nuevos Controladores (Sistema de Reservas y Domicilios)
use App\Http\Controllers\Api\TelemetryController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\RouteController;

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


/* AZURE IOT CENTRAL - Posible integracion esta por verse

Route::prefix('azure')->group(function () {
    Route::put('/device/{deviceId}', [AzureDeviceController::class, 'registerDevice']);
    Route::get('/telemetria/{deviceId}', [AzureTelemetryController::class, 'getDeviceTelemetry']);
});
*/


// ESTADO DEL API
// Rate limit: 120 requests/min para endpoints públicos

Route::middleware('throttle:public')->group(function () {
Route::get('/status', function () {
    return response()->json([
        'success' => true,
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
}); // Fin throttle:public


// TELEMETRÍA - SOLO LECTURA (La escritura es vía MQTT Listener)
// ✅ PROTEGIDO: Requiere autenticación y permiso ver_telemetria

Route::middleware('auth:sanctum')->prefix('telemetria')->group(function () {
    // Consultas de telemetría
    Route::get('/latest', [TelemetryController::class, 'latest'])
        ->middleware('can:view-telemetry');
    Route::get('/realtime', [TelemetryController::class, 'realtime'])
        ->middleware('can:view-telemetry');
    Route::get('/{deviceId}', [TelemetryController::class, 'show'])
        ->middleware('can:view-telemetry');
    Route::get('/{deviceId}/history', [TelemetryController::class, 'history'])
        ->middleware('can:view-telemetry');
    Route::get('/{deviceId}/route', [TelemetryController::class, 'getRoute'])
        ->middleware('can:view-telemetry');
});

// DISPOSITIVOS - FILTROS Y BÚSQUEDAS
// ✅ PROTEGIDO: Solo administradores pueden ver dispositivos IoT

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('devices')->group(function () {
    Route::get('/', [DeviceController::class, 'index']);
    Route::get('/{deviceId}', [DeviceController::class, 'show']);
    Route::get('/type/{type}', [DeviceController::class, 'byType']);
    Route::get('/status/{status}', [DeviceController::class, 'byStatus']);
    Route::get('/branch/{branch}', [DeviceController::class, 'byBranch']);
    Route::get('/maintenance/required', [DeviceController::class, 'needsMaintenance']);
});

// ESTADÍSTICAS Y MÉTRICAS
// ✅ PROTEGIDO: Solo administradores pueden ver estadísticas del sistema

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('stats')->group(function () {
    Route::get('/', [StatsController::class, 'general']);
    Route::get('/dashboard', [StatsController::class, 'dashboard']);
    Route::get('/branches', [StatsController::class, 'branches']);
    Route::get('/battery', [StatsController::class, 'battery']);
    Route::get('/maintenance', [StatsController::class, 'maintenance']);
});


// VEHÍCULOS - CATÁLOGO Y DISPONIBILIDAD

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
    
    // Tracking (requiere autenticación)
    Route::get('/{vehicle}/location', [VehicleController::class, 'location'])
        ->middleware('auth:sanctum');
    
    // Stats (admin)
    Route::get('/{vehicle}/stats', [VehicleController::class, 'stats'])
        ->middleware('auth:sanctum');
});

// SEDES - UBICACIONES Y COBERTURA

Route::prefix('branches')->group(function () {
    Route::get('/', [BranchController::class, 'index']);
    Route::get('/{branch}', [BranchController::class, 'show']);
    Route::get('/{branch}/vehicles', [BranchController::class, 'vehicles'])
        ->middleware('auth:sanctum');
    Route::get('/{branch}/stats', [BranchController::class, 'stats'])
        ->middleware('auth:sanctum');
    
    // Búsqueda geográfica
    Route::post('/nearest', [BranchController::class, 'nearest']);
    Route::post('/in-radius', [BranchController::class, 'inRadius']);
});

// RUTAS OSRM - CÁLCULO DE RUTAS Y NAVEGACIÓN
// ✅ PROTEGIDO: Requiere autenticación para evitar abuso

Route::middleware('auth:sanctum')->prefix('routes')->group(function () {
    // Cálculo de rutas
    Route::post('/calculate', [RouteController::class, 'calculate'])
        ->middleware('throttle:60,1'); // 60 requests por minuto
    
    // Map matching (ajustar GPS a carreteras)
    Route::post('/match', [RouteController::class, 'match'])
        ->middleware('throttle:60,1');
    
    // Estimar tiempo de llegada
    Route::post('/eta', [RouteController::class, 'estimateArrival'])
        ->middleware('throttle:120,1'); // Más requests para tracking en tiempo real
    
    // Optimizar ruta con múltiples paradas
    Route::post('/optimize', [RouteController::class, 'optimize'])
        ->middleware('throttle:30,1');
    
    // Matriz de distancias
    Route::post('/matrix', [RouteController::class, 'distanceMatrix'])
        ->middleware('throttle:30,1');
    
    // Health check (público)
    Route::get('/health', [RouteController::class, 'health'])
        ->withoutMiddleware('auth:sanctum')
        ->middleware('throttle:10,1');
});

// RESERVAS - REQUIERE AUTENTICACIÓN

Route::middleware('auth:sanctum')->prefix('reservations')->group(function () {
    // CRUD básico (admin)
    Route::get('/', [ReservationController::class, 'index'])
        ->middleware('can:viewAny,App\Models\Reservation');
    Route::post('/', [ReservationController::class, 'store'])
        ->middleware('throttle:reservations');
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

// DOMICILIOS - REQUIERE AUTENTICACIÓN

Route::middleware('auth:sanctum')->prefix('deliveries')->group(function () {
    // CRUD
    Route::get('/', [DeliveryController::class, 'index'])
        ->middleware('role:admin');
    Route::post('/package', [DeliveryController::class, 'storePackage']);
    Route::post('/vehicle', [DeliveryController::class, 'storeVehicle']);
    Route::get('/{delivery}', [DeliveryController::class, 'show']);
    
    // Acciones
    Route::post('/{delivery}/assign-driver', [DeliveryController::class, 'assignDriver'])
        ->middleware('role:admin');
    Route::post('/{delivery}/start', [DeliveryController::class, 'start']);
    Route::post('/{delivery}/complete', [DeliveryController::class, 'complete']);
    
    // Tracking
    Route::get('/{delivery}/track', [DeliveryController::class, 'track']);
    
    // Mis domicilios
    Route::get('/me/list', [DeliveryController::class, 'myDeliveries']);
    
    // Para conductores
    Route::get('/me/assigned', [DeliveryController::class, 'myAssignedDeliveries'])
        ->middleware('role:conductor');
    
    // Para admin
    Route::get('/pending/list', [DeliveryController::class, 'pending'])
        ->middleware('role:admin');
});

// PAGOS - REQUIERE AUTENTICACIÓN

    // Métodos disponibles
Route::get('/payments/methods/available', [PaymentController::class, 'paymentMethods']);

Route::middleware('auth:sanctum')->prefix('payments')->group(function () {
    // Crear intención de pago
    Route::post('/reservations/{reservation}/create-intent', [PaymentController::class, 'createPaymentIntent'])
        ->middleware('throttle:payments');
    
    // Procesar pago
    Route::post('/{payment}/process', [PaymentController::class, 'processPayment'])
        ->middleware('throttle:payments');
    
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

});

// RUTA 404 PERSONALIZADA

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint no encontrado',
        'documentation' => [
            'telemetria' => '/api/telemetria/*',
            'vehiculos' => '/api/vehicles/*',
            'sedes' => '/api/branches/*',
            'rutas' => '/api/routes/* (auth)',
            'reservas' => '/api/reservations/* (auth)',
            'domicilios' => '/api/deliveries/* (auth)',
            'pagos' => '/api/payments/* (auth)',
        ]
    ], 404);
});