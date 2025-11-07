<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard - SimpleDashboard
    Route::get('dashboard', App\Livewire\Dashboard\SimpleDashboard::class)->name('dashboard');

});

Route::view('reports', 'admin.reports.index')->name('reports.index');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Gestión
    Route::view('users', 'admin.users.index')->name('users.index');
    Route::view('drivers', 'admin.drivers.index')->name('drivers.index');
    Route::view('vehicles', 'admin.vehicles.index')->name('vehicles.index');
    Route::view('reservations', 'admin.reservations.index')->name('reservations.index');
    
    // Monitoreo y Mapas (NUEVOS)
    Route::view('map', 'admin.map')->name('map');
    Route::view('monitoring', 'admin.monitoring')->name('monitoring');
    
    // Reportes (para el futuro)
    Route::view('reports', 'admin.reports.index')->name('reports.index');
});

// Rutas públicas del catálogo (para el futuro)
Route::middleware(['auth'])->prefix('catalog')->name('catalog.')->group(function () {
    Route::view('/', 'catalog.index')->name('index');
    Route::view('vehicle/{id}', 'catalog.vehicle-detail')->name('vehicle.detail');
    Route::view('my-reservations', 'catalog.my-reservations')->name('reservations');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';