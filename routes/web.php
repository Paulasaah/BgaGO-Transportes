<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'landing.home')->name('home');

// Secciones públicas
Route::view('mapa', 'landing.mapa')->name('mapa');
Route::view('servicios', 'landing.servicios')->name('servicios');
Route::view('catalogo', 'landing.catalogo')->name('catalogo');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('users', 'admin.users.index')->name('users.index');
    Route::view('drivers', 'admin.drivers.index')->name('drivers.index');
    Route::view('vehicles', 'admin.vehicles.index')->name('vehicles.index');
    Route::view('reservations', 'admin.reservations.index')->name('reservations.index');
});

// Rutas de Usuario autenticado para Reservas y Pagos
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('reservar', 'usuario.reservar')->name('reservar');
    Route::view('mis-reservas', 'usuario.mis-reservas')->name('mis-reservas');
    Route::view('pago/{reserva}', 'usuario.pago')->name('pago');
    Route::view('confirmacion-pago/{pago}', 'usuario.confirmacion')->name('confirmacion-pago');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
