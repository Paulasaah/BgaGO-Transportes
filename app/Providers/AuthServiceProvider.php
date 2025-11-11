<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Importar modelos
use App\Models\Reservation;
use App\Models\Delivery;
use App\Models\Vehicle;
use App\Models\Payment;
use App\Models\Branch;
use App\Models\User;
use App\Models\DriverProfile;

// Importar policies
use App\Policies\ReservationPolicy;
use App\Policies\DeliveryPolicy;
use App\Policies\VehiclePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\BranchPolicy;
use App\Policies\UserPolicy;
use App\Policies\DriverProfilePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Reservation::class => ReservationPolicy::class,
        Delivery::class => DeliveryPolicy::class,
        Vehicle::class => VehiclePolicy::class,
        Payment::class => PaymentPolicy::class,
        Branch::class => BranchPolicy::class,
        User::class => UserPolicy::class,
        DriverProfile::class => DriverProfilePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // ==========================================
        // SUPER ADMIN BYPASS
        // ==========================================
        // Super admin puede hacer TODO sin restricciones
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }
        });

        // ==========================================
        // GATES PERSONALIZADOS
        // ==========================================

        // Ver dashboard (admin, conductor)
        Gate::define('view-dashboard', function ($user) {
            return $user->hasAnyRole(['admin', 'super_admin', 'conductor']);
        });

        // Gestionar sistema (solo admin)
        Gate::define('manage-system', function ($user) {
            return $user->hasRole(['admin', 'super_admin']);
        });

        // Ver reportes
        Gate::define('view-reports', function ($user) {
            return $user->hasPermissionTo('ver_reportes') || 
                   $user->hasRole(['admin', 'super_admin']);
        });

        // Gestionar pagos
        Gate::define('manage-payments', function ($user) {
            return $user->hasPermissionTo('aprobar_pagos') || 
                   $user->hasRole(['admin', 'super_admin']);
        });

        // Ver estadísticas de vehículos
        Gate::define('view-vehicle-stats', function ($user) {
            return $user->hasRole(['admin', 'super_admin']) ||
                   $user->hasPermissionTo('ver_reportes');
        });

        // Asignar conductores
        Gate::define('assign-drivers', function ($user) {
            return $user->hasRole(['admin', 'super_admin', 'dispatcher']) ||
                   $user->hasPermissionTo('asignar_conductores');
        });

        // Ver telemetría
        Gate::define('view-telemetry', function ($user) {
            return $user->hasAnyRole(['admin', 'super_admin', 'conductor']);
        });

        // Gestionar sedes
        Gate::define('manage-branches', function ($user) {
            return $user->hasRole(['admin', 'super_admin']);
        });

        // Ver logs del sistema
        Gate::define('view-logs', function ($user) {
            return $user->hasRole(['admin', 'super_admin']);
        });
    }
}