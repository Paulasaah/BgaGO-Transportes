<?php

use Illuminate\Support\Facades\Broadcast;

/**
 * Canal privado: Notificaciones de usuario
 * Solo el usuario autenticado puede escuchar su propio canal
 */
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Canal público: Telemetría en tiempo real
 * Todos los usuarios pueden escuchar actualizaciones de telemetría
 * Usado para actualizar el mapa de vehículos en tiempo real
 */
Broadcast::channel('telemetry', function () {
    return true; // Canal público - sin autenticación requerida
});
