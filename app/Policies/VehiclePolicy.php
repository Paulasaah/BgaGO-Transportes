<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Auth\Access\HandlesAuthorization;

class VehiclePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        // Todos los usuarios autenticados pueden ver el catálogo
        return true;
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        // Todos pueden ver vehículos del catálogo
        return $vehicle->visible_catalogo || 
               $user->hasRole(['admin', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin']) ||
               $user->hasPermissionTo('gestionar_vehiculos');
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->hasRole(['admin', 'super_admin']) ||
               $user->hasPermissionTo('gestionar_vehiculos');
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    public function assignDriver(User $user, Vehicle $vehicle): bool
    {
        return $user->hasRole(['admin', 'super_admin', 'dispatcher']) ||
               $user->hasPermissionTo('asignar_conductores');
    }

    public function viewMaintenance(User $user, Vehicle $vehicle): bool
    {
        return $user->hasRole(['admin', 'super_admin']) ||
               $user->hasPermissionTo('ver_mantenimientos');
    }
}