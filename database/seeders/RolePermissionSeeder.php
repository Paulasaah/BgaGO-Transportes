<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->warn('🔄 Verificando roles y permisos existentes...');

        
        $permissions = [
            // Permisos usados en controllers
            'manage-payments',  // Usado en PaymentController
            'view-vehicle-stats',  // Usado en VehicleController
            
            // Reservas
            'ver_reservas',
            'crear_reservas',
            'editar_reservas',
            'cancelar_reservas',

            // Domicilios
            'ver_domicilios',
            'crear_domicilios',
            'asignar_domicilios',
            'completar_domicilios',

            // Vehículos
            'ver_vehiculos',
            'gestionar_vehiculos',
            'asignar_conductores',

            // Pagos
            'ver_pagos',
            'aprobar_pagos',
            'rechazar_pagos',
            'reembolsar_pagos',

            // Conductores
            'ver_conductores',
            'gestionar_conductores',

            // Dashboard
            'ver_dashboard_admin',
            'ver_dashboard_conductor',

            // Reportes
            'ver_reportes',
            'exportar_reportes',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('✅ Permisos creados o actualizados correctamente.');

        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $conductorRole = Role::firstOrCreate(['name' => 'conductor', 'guard_name' => 'web']);
        $clienteRole = Role::firstOrCreate(['name' => 'cliente', 'guard_name' => 'web']);

        // ADMIN - Todos los permisos
        $adminRole->syncPermissions(Permission::all());

        // CONDUCTOR
        $conductorRole->syncPermissions([
            'ver_domicilios',
            'completar_domicilios',
            'ver_reservas',
            'ver_dashboard_conductor',
        ]);

        // CLIENTE
        $clienteRole->syncPermissions([
            'ver_reservas',
            'crear_reservas',
            'cancelar_reservas',
            'ver_domicilios',
            'crear_domicilios',
            'ver_pagos',
        ]);

        $this->command->info('✅ Roles y permisos asignados correctamente.');
    }
}
