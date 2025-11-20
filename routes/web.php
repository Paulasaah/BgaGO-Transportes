<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Services\RouteService;
use Livewire\Volt\Volt;
use App\Http\Controllers\Admin\{
    DashboardController,
    UserController,
    DriverController,
    VehicleController,
    ReservationController,
    GeocodingController
};
use App\Http\Controllers\Api\RouteController as ApiRouteController;

/*
|--------------------------------------------------------------------------
| 🌍 Página principal (pública)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        $u = Auth::user();
        if ($u->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($u->isDriver()) {
            return redirect('/driver/dashboard');
        }
        return redirect()->route('user.home');
    }
    return view('landing.home');
})->name('home');
Route::redirect('/home', '/');
Route::view('/mapa', 'landing.mapa')->name('landing.mapa');


/*
|--------------------------------------------------------------------------
| 👤 Dashboard de usuario autenticado
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', App\Livewire\Dashboard\SimpleDashboard::class)->name('dashboard');
    Route::view('home-usuario', 'user.home-usuario')->name('user.home');
});


/*
|--------------------------------------------------------------------------
| 🛠️ Panel de Administración (solo admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // DASHBOARD
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // === USUARIOS ===
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // === CONDUCTORES ===
    Route::get('drivers', [DriverController::class, 'index'])->name('drivers.index');
    Route::patch('drivers/{user}/toggle-status', [DriverController::class, 'toggleStatus'])->name('drivers.toggle-status');

    // === VEHÍCULOS ===
    Route::get('vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
    Route::post('vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::get('vehicles/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::get('vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
    Route::put('vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');

    // === RESERVAS ===
    Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::patch('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // === MANTENIMIENTOS ===
    Route::get('maintenances', [\App\Http\Controllers\Admin\MaintenanceController::class, 'index'])->name('maintenances.index');
    Route::get('maintenances/create', [\App\Http\Controllers\Admin\MaintenanceController::class, 'create'])->name('maintenances.create');
    Route::post('maintenances', [\App\Http\Controllers\Admin\MaintenanceController::class, 'store'])->name('maintenances.store');
    Route::get('maintenances/{maintenance}', [\App\Http\Controllers\Admin\MaintenanceController::class, 'show'])->name('maintenances.show');
    Route::get('maintenances/{maintenance}/edit', [\App\Http\Controllers\Admin\MaintenanceController::class, 'edit'])->name('maintenances.edit');
    Route::put('maintenances/{maintenance}', [\App\Http\Controllers\Admin\MaintenanceController::class, 'update'])->name('maintenances.update');
    Route::delete('maintenances/{maintenance}', [\App\Http\Controllers\Admin\MaintenanceController::class, 'destroy'])->name('maintenances.destroy');
    Route::patch('maintenances/{maintenance}/start', [\App\Http\Controllers\Admin\MaintenanceController::class, 'start'])->name('maintenances.start');
    Route::patch('maintenances/{maintenance}/complete', [\App\Http\Controllers\Admin\MaintenanceController::class, 'complete'])->name('maintenances.complete');
    Route::patch('maintenances/{maintenance}/cancel', [\App\Http\Controllers\Admin\MaintenanceController::class, 'cancel'])->name('maintenances.cancel');

    // === MONITOREO Y MAPAS ===
    Route::view('map', 'admin.map')->name('map');
    Route::view('monitoring', 'admin.monitoring')->name('monitoring');

    // === REPORTES ===
    Route::view('reports', 'admin.reports.index')->name('reports.index');

    // GEOCODING (para autocompletado en panel admin)
    Route::get('geocode/search', [GeocodingController::class, 'search'])
        ->name('geocode.search');
});


/*
|--------------------------------------------------------------------------
| 🚗 Catálogo (usuario autenticado)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->prefix('catalog')
    ->name('catalog.')
    ->group(function () {
        Route::view('/', 'catalog.index')->name('index');
        Route::view('vehicle/{id}', 'catalog.vehicle-detail')->name('vehicle.detail');
        Route::view('my-reservations', 'catalog.my-reservations')->name('reservations');

        Route::post('reservations/{reservation}/cancel', [\App\Http\Controllers\Api\ReservationController::class, 'cancel'])
            ->name('reservations.cancel');

        // VISTAS NUEVAS
        Route::view('reserve', 'user.reservar')->name('reserve');
    Route::view('payment', 'user.pago')->name('payment');
    Route::view('receipt', 'user.recibo')->name('receipt');
    Route::view('confirmation', 'user.confirmacion')->name('confirmation');
    });


// Servicios (domicilios)
Route::middleware(['auth'])->group(function () {
    Route::view('servicios', 'user.domicilio')->name('services.delivery');
    Volt::route('wallet', 'user.wallet')->name('wallet');
    Route::get('geocode/search', [\App\Http\Controllers\Admin\GeocodingController::class, 'search'])->name('user.geocode.search');
});

// Health OSRM (solo autenticado)
Route::middleware(['auth'])->get('/health/osrm', function (RouteService $routeService) {
    $result = $routeService->healthCheck();
    return response()->json($result, $result['success'] ? 200 : 503);
})->name('health.osrm');


/*
|--------------------------------------------------------------------------
| ⚙️ Configuración de Usuario (Volt)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});


/*
|--------------------------------------------------------------------------
| 🧪 Ruta de pruebas
|--------------------------------------------------------------------------
*/
Route::view('/test-map', 'test-map');


/*
|--------------------------------------------------------------------------
| 🚚 Panel de Conductor (solo conductor)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:conductor'])
    ->prefix('driver')
    ->name('driver.')
    ->group(function () {
        Livewire\Volt\Volt::route('dashboard', 'driver.dashboard')->name('dashboard');
    });


/*
|--------------------------------------------------------------------------
| 🔐 Autenticación
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
