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
Route::get('/', fn() => view('welcome'))->name('home');

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
    Route::resource('users', UserController::class);

    // === CONDUCTORES ===
    Route::get('drivers', [DriverController::class, 'index'])->name('drivers.index');
    Route::get('drivers/{user}', [DriverController::class, 'show'])->name('drivers.show');
    Route::get('drivers/{user}/edit', [DriverController::class, 'edit'])->name('drivers.edit');
    Route::put('drivers/{user}', [DriverController::class, 'update'])->name('drivers.update');
    Route::patch('drivers/{user}/toggle-status', [DriverController::class, 'toggleStatus'])->name('drivers.toggle-status');

    // === VEHÍCULOS ===
    Route::resource('vehicles', VehicleController::class);

    // === RESERVAS ===
    Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::patch('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // === MONITOREO Y MAPAS ===
    Route::view('map', 'admin.vehicles.map')->name('map');
    Route::view('monitoring', 'admin.vehicles.monitoring')->name('monitoring');

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

// =========================================
// 🧪 Ruta de pruebas
// =========================================
Route::view('/test-map', 'test-map');

// =========================================
// 🔐 Autenticación
// =========================================
require __DIR__.'/auth.php';
