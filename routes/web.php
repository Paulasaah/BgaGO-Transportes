<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\Admin\{
    DashboardController,
    UserController,
    DriverController,
    VehicleController,
    ReservationController
};

// =========================================
// 🌍 Página principal (pública)
// =========================================
Route::get('/', function () {
    return view('welcome');
})->name('home');


// =========================================
// 👤 Dashboard de usuario autenticado
// =========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', App\Livewire\Dashboard\SimpleDashboard::class)->name('dashboard');
});


// =========================================
// 🛠️ Panel de Administración (solo admin)
// =========================================
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // === DASHBOARD ===
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
    Route::get('reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::patch('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // === MONITOREO Y MAPAS ===
    Route::view('map', 'admin.map')->name('map');
    Route::view('monitoring', 'admin.monitoring')->name('monitoring');

    // === REPORTES ===
    Route::view('reports', 'admin.reports.index')->name('reports.index');
});


// =========================================
// 🚗 Catálogo (usuario autenticado)
// =========================================
Route::middleware(['auth'])
    ->prefix('catalog')
    ->name('catalog.')
    ->group(function () {
        Route::view('/', 'catalog.index')->name('index');
        Route::view('vehicle/{id}', 'catalog.vehicle-detail')->name('vehicle.detail');
        Route::view('my-reservations', 'catalog.my-reservations')->name('reservations');
    });


// =========================================
// ⚙️ Configuración de Usuario (Volt)
// =========================================
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::get('/presentacion', function () {
    return view('presentacion');
})->name('presentacion');

require __DIR__.'/auth.php';

// =========================================
// 🧪 Ruta de pruebas
// =========================================
Route::view('/test-map', 'test-map');

// =========================================
// 🔐 Autenticación
// =========================================
require __DIR__.'/auth.php';
