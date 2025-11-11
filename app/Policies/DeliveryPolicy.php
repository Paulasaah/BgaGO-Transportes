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
        return $user->hasRole(['admin', 'super_admin']);
    }

    public function view(User $user, Delivery $delivery): bool
    {
        return $user->hasRole(['admin', 'super_admin'])
            || $delivery->conductor_id === $user->id
            || ($delivery->reservation && $delivery->reservation->user_id === $user->id);
    }

    public function accept(User $user, Delivery $delivery): bool
    {
        return $user->hasRole('conductor')
            && $delivery->estado === 'pendiente'
            && is_null($delivery->conductor_id);
    }

    public function start(User $user, Delivery $delivery): bool
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        $asignado = $delivery->conductor_id === $user->id
            || ($delivery->reservation && $delivery->reservation->conductor_id === $user->id);

        return $user->hasRole('conductor')
            && $asignado
            && in_array($delivery->estado, ['asignado', 'confirmado']);
    }

    public function complete(User $user, Delivery $delivery): bool
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        $asignado = $delivery->conductor_id === $user->id
            || ($delivery->reservation && $delivery->reservation->conductor_id === $user->id);

        return $user->hasRole('conductor')
            && $asignado
            && in_array($delivery->estado, ['en_camino']);
    }

    public function cancel(User $user, Delivery $delivery): bool
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        return $user->hasRole('conductor')
            && in_array($delivery->estado, ['pendiente', 'asignado', 'confirmado']);
    }

    public function viewStats(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin'])
            || $user->hasPermissionTo('ver_reportes');
    }
}
