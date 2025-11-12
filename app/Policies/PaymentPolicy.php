<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin']) ||
               $user->hasPermissionTo('ver_pagos');
    }

    public function view(User $user, Payment $payment): bool
    {
        // Admin puede ver cualquier pago
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // Usuario puede ver sus propios pagos
        return $payment->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        // Cualquier usuario autenticado puede crear intención de pago
        return true;
    }

    public function approve(User $user, Payment $payment): bool
    {
        return $user->hasRole(['admin', 'super_admin']) ||
               $user->hasPermissionTo('aprobar_pagos');
    }

    public function reject(User $user, Payment $payment): bool
    {
        return $user->hasRole(['admin', 'super_admin']) ||
               $user->hasPermissionTo('rechazar_pagos');
    }

    public function refund(User $user, Payment $payment): bool
    {
        return $user->hasRole(['admin', 'super_admin']) ||
               $user->hasPermissionTo('reembolsar_pagos');
    }
}