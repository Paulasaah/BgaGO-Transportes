<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Auth\Access\HandlesAuthorization;

class BranchPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        // Todos los usuarios pueden ver sedes públicas
        return true;
    }

    public function view(User $user, Branch $branch): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    public function update(User $user, Branch $branch): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    public function delete(User $user, Branch $branch): bool
    {
        return $user->hasRole('super_admin');
    }

    public function viewStats(User $user, Branch $branch): bool
    {
        return $user->hasRole(['admin', 'super_admin']) ||
               $user->hasPermissionTo('ver_reportes');
    }
}
