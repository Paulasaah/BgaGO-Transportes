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
        // Resetear cache de roles/permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ==========================================
        // CREAR PERMISOS
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
            Permission::create(['name' => $permission]);
        }

        // ==========================================
        // CREAR ROLES Y ASIGNAR PERMISOS
        // ==========================================

        // 🔴 ADMIN - Acceso total
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // 🟢 CONDUCTOR - Gestión de entregas
        $conductorRole = Role::create(['name' => 'conductor']);
        $conductorRole->givePermissionTo([
            'ver_domicilios',
            'completar_domicilios',
            'ver_reservas',
            'ver_dashboard_conductor',
        ]);

        // 🔵 CLIENTE - Usuario final
        $clienteRole = Role::create(['name' => 'cliente']);
        $clienteRole->givePermissionTo([
            'ver_reservas',
            'crear_reservas',
            'cancelar_reservas',
            'ver_domicilios',
            'crear_domicilios',
            'ver_pagos',
        ]);

        $this->command->info('✅ Roles y permisos creados correctamente');

        // ==========================================
        // ASIGNAR ROLES A USUARIOS EXISTENTES
        // ==========================================
        
        // Primer usuario = Admin
        $admin = User::first();
        if ($admin) {
            $admin->assignRole('admin');
            $this->command->info("✅ {$admin->email} asignado como ADMIN");
        }

        // Usuarios 2-4 = Conductores
        $conductores = User::skip(1)->take(3)->get();
        foreach ($conductores as $conductor) {
            $conductor->assignRole('conductor');
            $this->command->info("✅ {$conductor->email} asignado como CONDUCTOR");
        }

        // Usuarios restantes = Clientes
        $totalUsers = User::count();

        if ($totalUsers > 4) {
            $clientes = User::skip(4)->take($totalUsers - 4)->get();
            foreach ($clientes as $cliente) {
                $cliente->assignRole('cliente');
                $this->command->info("✅ {$cliente->email} asignado como CLIENTE");
            }
        } else {
            $this->command->warn('⚠️ No hay usuarios adicionales para asignar como CLIENTES.');
        }
    }
}