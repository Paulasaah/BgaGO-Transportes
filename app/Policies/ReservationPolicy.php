<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reservation;
use App\Enums\ReservationStatus;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
{
    use HandlesAuthorization;

    /**
     * Determinar si el usuario puede ver TODAS las reservas (admin)
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    /**
     * Determinar si el usuario puede ver UNA reserva específica
     */
    public function view(User $user, Reservation $reservation): bool
    {
        // Admin puede ver cualquier reserva
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // Cliente puede ver sus propias reservas
        if ($reservation->user_id === $user->id) {
            return true;
        }

        // Conductor puede ver reservas asignadas a él
        if ($reservation->conductor_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determinar si el usuario puede crear reservas
     */
    public function create(User $user): bool
    {
        // Clientes y admins pueden crear reservas
        return $user->hasRole(['cliente', 'admin', 'super_admin']) || 
               $user->hasPermissionTo('crear_reservas');
    }

    /**
     * Determinar si el usuario puede actualizar una reserva
     */
    public function update(User $user, Reservation $reservation): bool
    {
        // Admin puede actualizar cualquier reserva
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // Cliente puede actualizar su propia reserva si no está finalizada
        if ($reservation->user_id === $user->id) {
            return !$reservation->isFinal();
        }

        // Conductor asignado puede actualizar estado
        if ($reservation->conductor_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determinar si el usuario puede cancelar una reserva
     */
    public function cancel(User $user, Reservation $reservation): bool
    {
        // Admin puede cancelar cualquier reserva
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // Cliente puede cancelar su propia reserva si está en estado cancelable
        if ($reservation->user_id === $user->id) {
            return $reservation->canBeCancelled();
        }

        return false;
    }

    /**
     * Determinar si el usuario puede confirmar una reserva (después del pago)
     */
    public function confirm(User $user, Reservation $reservation): bool
    {
        // Solo admin o el sistema pueden confirmar
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // El cliente dueño puede confirmar si tiene pago aprobado
        if ($reservation->user_id === $user->id && $reservation->hasPaidPayment()) {
            return $reservation->isPendiente();
        }

        return false;
    }

    /**
     * Determinar si el usuario puede iniciar una reserva
     */
    public function start(User $user, Reservation $reservation): bool
    {
        // Admin puede iniciar cualquier reserva
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // Conductor asignado puede iniciar
        if ($reservation->conductor_id === $user->id) {
            return $reservation->isConfirmada();
        }

        // Cliente puede iniciar su propia reserva confirmada
        if ($reservation->user_id === $user->id) {
            return $reservation->isConfirmada();
        }

        return false;
    }

    /**
     * Determinar si el usuario puede completar una reserva
     */
    public function complete(User $user, Reservation $reservation): bool
    {
        // Admin puede completar cualquier reserva
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // Conductor asignado puede completar
        if ($reservation->conductor_id === $user->id) {
            return $reservation->isActiva();
        }

        // Cliente puede completar su propia reserva activa
        if ($reservation->user_id === $user->id) {
            return $reservation->isActiva();
        }

        return false;
    }

    /**
     * Determinar si el usuario puede calificar una reserva
     */
    public function rate(User $user, Reservation $reservation): bool
    {
        // Solo el cliente que hizo la reserva puede calificar
        return $reservation->user_id === $user->id && 
               $reservation->isCompletada() &&
               is_null($reservation->calificacion_cliente);
    }

    /**
     * Determinar si el usuario puede eliminar una reserva
     */
    public function delete(User $user, Reservation $reservation): bool
    {
        // Solo admin puede eliminar (soft delete)
        return $user->hasRole(['admin', 'super_admin']);
    }

    /**
     * Determinar si el usuario puede restaurar una reserva eliminada
     */
    public function restore(User $user, Reservation $reservation): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    /**
     * Determinar si el usuario puede eliminar permanentemente
     */
    public function forceDelete(User $user, Reservation $reservation): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determinar si el usuario puede ver estadísticas de reservas
     */
    public function viewStats(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin']) || 
               $user->hasPermissionTo('ver_reportes');
    }

    /**
     * Determinar si el usuario puede asignar conductor
     */
    public function assignDriver(User $user, Reservation $reservation): bool
    {
        return $user->hasRole(['admin', 'super_admin']) && 
               $reservation->isDomicilio() &&
               $reservation->isPendiente();
    }
}