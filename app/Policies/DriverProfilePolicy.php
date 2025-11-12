<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DriverProfile;
use Illuminate\Auth\Access\HandlesAuthorization;

class DriverProfilePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin', 'dispatcher']) ||
               $user->hasPermissionTo('ver_conductores');
    }

    public function view(User $user, DriverProfile $driverProfile): bool
    {
        // Admin puede ver cualquier perfil
        if ($user->hasRole(['admin', 'super_admin', 'dispatcher'])) {
            return true;
        }

        // Conductor puede ver su propio perfil
        return $user->id === $driverProfile->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin']) ||
               $user->hasPermissionTo('gestionar_conductores');
    }

    public function update(User $user, DriverProfile $driverProfile): bool
    {
        // Admin puede actualizar cualquier perfil
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // Conductor puede actualizar su propio perfil (campos limitados)
        return $user->id === $driverProfile->user_id;
    }

    public function activate(User $user, DriverProfile $driverProfile): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    public function deactivate(User $user, DriverProfile $driverProfile): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    public function delete(User $user, DriverProfile $driverProfile): bool
    {
        return $user->hasRole('super_admin');
    }
}namespace App\Policies;

use App\Models\DriverProfile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DriverProfilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DriverProfile $driverProfile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DriverProfile $driverProfile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DriverProfile $driverProfile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DriverProfile $driverProfile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DriverProfile $driverProfile): bool
    {
        return false;
    }
}
