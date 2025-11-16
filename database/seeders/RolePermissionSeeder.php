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

        $this->command->warn('🔄 Creando roles y permisos del sistema...');

        // ==========================================
        // CREAR PERMISOS
        // ==========================================
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
            'ver_telemetria',

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

            // Sistema
            'ver_logs',
            'gestionar_sedes',
            'gestionar_usuarios',

            // Acciones
            'start-reservation',
            'complete-reservation',
            'start-delivery',
            'complete-delivery',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('✅ Permisos creados o actualizados correctamente.');

        // ==========================================
        // CREAR ROLES (4 roles: super_admin, admin, conductor, cliente)
        // ==========================================
        
        // 1. SUPER ADMIN - Bypass total (definido en AuthServiceProvider)
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());
        $this->command->info('✅ Rol super_admin creado con todos los permisos');

        // 2. ADMIN - Todos los permisos excepto force delete
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());
        $this->command->info('✅ Rol admin creado con todos los permisos');

        // 3. CONDUCTOR - Permisos operativos limitados
        $conductorRole = Role::firstOrCreate(['name' => 'conductor', 'guard_name' => 'web']);
        $conductorRole->syncPermissions([
            'ver_domicilios',
            'completar_domicilios',
            'ver_reservas',
            'ver_dashboard_conductor',
            'ver_telemetria',
            'start-reservation',
            'complete-reservation',
            'start-delivery',
            'complete-delivery',
        ]);
        $this->command->info('✅ Rol conductor creado con permisos operativos');

        // 4. CLIENTE - Permisos básicos de usuario
        $clienteRole = Role::firstOrCreate(['name' => 'cliente', 'guard_name' => 'web']);
        $clienteRole->syncPermissions([
            'ver_reservas',
            'crear_reservas',
            'cancelar_reservas',
            'ver_domicilios',
            'crear_domicilios',
            'ver_pagos',
            'ver_vehiculos',  // ✅ Agregado: Cliente necesita ver catálogo de vehículos
        ]);
        $this->command->info('✅ Rol cliente creado con permisos básicos');

        $this->command->info('');
        $this->command->info('🎉 Sistema de roles y permisos configurado correctamente');
        $this->command->info('📋 Roles creados: super_admin, admin, conductor, cliente');
        $this->command->info('🔐 Total de permisos: ' . Permission::count());
    }
}
