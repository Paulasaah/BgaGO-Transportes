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
        // ==========================================
        // 🧹 LIMPIAR CACHE DE PERMISOS
        // ==========================================
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->warn('🔄 Verificando roles y permisos existentes...');

        // ==========================================
        // 🔐 CREAR O ACTUALIZAR PERMISOS
        // ==========================================
        $permissions = [
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

        // ==========================================
        // 🧩 CREAR ROLES SI NO EXISTEN
        // ==========================================
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $conductorRole = Role::firstOrCreate(['name' => 'conductor', 'guard_name' => 'web']);
        $clienteRole = Role::firstOrCreate(['name' => 'cliente', 'guard_name' => 'web']);

        // ==========================================
        // 🔗 ASIGNAR PERMISOS A CADA ROL
        // ==========================================

        // 🔴 ADMIN - Acceso total
        $adminRole->syncPermissions(Permission::all());

        // 🟢 CONDUCTOR - Gestión de entregas
        $conductorRole->syncPermissions([
            'ver_domicilios',
            'completar_domicilios',
            'ver_reservas',
            'ver_dashboard_conductor',
        ]);

        // 🔵 CLIENTE - Usuario final
        $clienteRole->syncPermissions([
            'ver_reservas',
            'crear_reservas',
            'cancelar_reservas',
            'ver_domicilios',
            'crear_domicilios',
            'ver_pagos',
        ]);

        $this->command->info('✅ Roles creados y permisos asignados correctamente.');

        // ==========================================
        // 👥 ASIGNAR ROLES A USUARIOS
        // ==========================================

        // Primer usuario = Admin
        $admin = User::first();
        if ($admin) {
            $admin->syncRoles(['admin']);
            $this->command->info("✅ {$admin->email} asignado como ADMIN");
        }

        // Usuarios 2–4 = Conductores
        $conductores = User::skip(1)->take(3)->get();
        foreach ($conductores as $conductor) {
            $conductor->syncRoles(['conductor']);
            $this->command->info("✅ {$conductor->email} asignado como CONDUCTOR");
        }

        // Resto = Clientes
        $totalUsers = User::count();
        if ($totalUsers > 4) {
            $clientes = User::skip(4)->take($totalUsers - 4)->get();
            foreach ($clientes as $cliente) {
                $cliente->syncRoles(['cliente']);
                $this->command->info("✅ {$cliente->email} asignado como CLIENTE");
            }
        } else {
            $this->command->warn('⚠️ No hay usuarios adicionales para asignar como CLIENTES.');
        }

        $this->command->info('🎯 Seeder ejecutado correctamente sin duplicados.');
    }
}
