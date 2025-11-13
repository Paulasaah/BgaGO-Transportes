<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin']) ||
               $user->hasPermissionTo('ver_usuarios');
    }

    public function view(User $user, User $model): bool
    {
        // Admin puede ver cualquier usuario
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // Usuario puede ver su propio perfil
        return $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    public function update(User $user, User $model): bool
    {
        // Admin puede actualizar cualquier usuario
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // Usuario puede actualizar su propio perfil
        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        // Solo super admin puede eliminar usuarios
        // Y no puede eliminarse a sí mismo
        return $user->hasRole('super_admin') && $user->id !== $model->id;
    }

    public function assignRole(User $user, User $model): bool
    {
        return $user->hasRole(['admin', 'super_admin']) &&
               $user->id !== $model->id; // No puede cambiar su propio rol
    }

    public function viewDriverProfile(User $user, User $model): bool
    {
        // Admin puede ver cualquier perfil de conductor
        if ($user->hasRole(['admin', 'super_admin', 'dispatcher'])) {
            return true;
        }

        // Conductor puede ver su propio perfil
        return $user->id === $model->id && $user->hasRole('conductor');
    }
}