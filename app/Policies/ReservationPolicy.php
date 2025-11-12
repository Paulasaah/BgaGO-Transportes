<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reservation;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
{
    use HandlesAuthorization;

    /** Ver todas las reservas (solo admin o super_admin) */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    /** Ver una reserva específica */
    public function view(User $user, Reservation $reservation): bool
    {
        return $user->hasRole(['admin', 'super_admin'])
            || $reservation->user_id === $user->id
            || $reservation->conductor_id === $user->id;
    }

    /** Crear reservas */
    public function create(User $user): bool
    {
        return $user->hasRole(['cliente', 'admin', 'super_admin'])
            || $user->hasPermissionTo('crear_reservas');
    }

    /** Actualizar una reserva */
    public function update(User $user, Reservation $reservation): bool
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // Cliente puede editar su propia reserva si no está finalizada
        if ($reservation->user_id === $user->id) {
            return !$reservation->isFinal();
        }

        // Conductor asignado puede modificar estados operativos
        if ($reservation->conductor_id === $user->id) {
            return !$reservation->isFinal();
        }

        return false;
    }

    /** Cancelar una reserva */
    public function cancel(User $user, Reservation $reservation): bool
    {
        // 👑 Admin o SuperAdmin pueden cancelar cualquier reserva (independiente del estado)
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // 👥 Cliente puede cancelar su propia reserva si el estado lo permite
        if ($reservation->user_id === $user->id) {
            return $reservation->canBeCancelled();
        }

        // 🚗 Conductor normalmente no puede cancelar
        return false;
    }

    /** Confirmar una reserva */
    public function confirm(User $user, Reservation $reservation): bool
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        return $reservation->user_id === $user->id
            && $reservation->hasPaidPayment()
            && $reservation->isPendiente();
    }

    /** Iniciar una reserva */
    public function start(User $user, Reservation $reservation): bool
    {
        // 👑 Admin o SuperAdmin pueden iniciar cualquier reserva
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // 🚗 Conductor asignado puede iniciar si está confirmada o pendiente
        if ($reservation->conductor_id === $user->id) {
            return $reservation->isConfirmada() || $reservation->isPendiente();
        }

        // 👤 Cliente puede iniciar su propia reserva confirmada
        if ($reservation->user_id === $user->id) {
            return $reservation->isConfirmada();
        }

        return false;
    }

    /** Completar una reserva */
    public function complete(User $user, Reservation $reservation): bool
    {
        // 👑 Admin o SuperAdmin pueden completar cualquier reserva
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // 🚗 Conductor asignado puede completar si la reserva está activa o en curso
        if ($reservation->conductor_id === $user->id) {
            return $reservation->isActiva() || $reservation->isEnCurso();
        }

        // 👤 Cliente puede completar su propia reserva activa
        if ($reservation->user_id === $user->id) {
            return $reservation->isActiva();
        }

        return false;
    }

    /** Calificar una reserva (solo cliente) */
    public function rate(User $user, Reservation $reservation): bool
    {
        return $reservation->user_id === $user->id &&
               $reservation->isCompletada() &&
               is_null($reservation->calificacion_cliente);
    }

    /** Eliminar (soft delete) */
    public function delete(User $user, Reservation $reservation): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    /** Restaurar reserva eliminada */
    public function restore(User $user, Reservation $reservation): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    /** Eliminación permanente */
    public function forceDelete(User $user, Reservation $reservation): bool
    {
        return $user->hasRole('super_admin');
    }

    /** Ver estadísticas */
    public function viewStats(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin'])
            || $user->hasPermissionTo('ver_reportes');
    }

    /** Asignar conductor */
    public function assignDriver(User $user, Reservation $reservation): bool
    {
        return $user->hasRole(['admin', 'super_admin'])
            && $reservation->isDomicilio()
            && $reservation->isPendiente();
    }
}
