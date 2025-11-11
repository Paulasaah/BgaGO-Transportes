<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Delivery;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeliveryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin', 'dispatcher']);
    }

    public function view(User $user, Delivery $delivery): bool
    {
        // Admin puede ver cualquier delivery
        if ($user->hasRole(['admin', 'super_admin', 'dispatcher'])) {
            return true;
        }

        // Cliente puede ver sus propios deliveries
        if ($delivery->user_id === $user->id) {
            return true;
        }

        // Conductor puede ver deliveries asignados
        if ($delivery->reservation && $delivery->reservation->conductor_id === $user->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['cliente', 'admin', 'super_admin']) || 
               $user->hasPermissionTo('crear_domicilios');
    }

    public function assignDriver(User $user, Delivery $delivery): bool
    {
        return $user->hasRole(['admin', 'super_admin', 'dispatcher']) &&
               $delivery->reservation->isPendiente();
    }

    public function start(User $user, Delivery $delivery): bool
    {
        // Solo el conductor asignado puede iniciar
        return $delivery->reservation && 
               $delivery->reservation->conductor_id === $user->id &&
               $delivery->canStart();
    }

    public function complete(User $user, Delivery $delivery): bool
    {
        // Solo el conductor asignado puede completar
        return $delivery->reservation && 
               $delivery->reservation->conductor_id === $user->id &&
               $delivery->canComplete();
    }

    public function cancel(User $user, Delivery $delivery): bool
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        return $delivery->user_id === $user->id && 
               $delivery->reservation->canBeCancelled();
    }
}