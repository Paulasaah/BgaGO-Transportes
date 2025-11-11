<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles base si no existen
        $roles = ['admin', 'cliente', 'conductor'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Usuarios base
        $users = [
            [
                'name' => 'Admin BgaGO',
                'email' => 'admin@bgago.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Cliente Test',
                'email' => 'cliente@bgago.com',
                'role' => 'cliente',
            ],
            [
                'name' => 'Conductor Test',
                'email' => 'conductor@bgago.com',
                'role' => 'conductor',
            ],
        ];

        // Limpiar tokens previos
        $tokensPath = storage_path('test-tokens.txt');
        if (file_exists($tokensPath)) {
            unlink($tokensPath);
        }

        // Crear usuarios y generar tokens
        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'updated_at' => now(),
                ]
            );

            // Sincronizar rol
            $user->syncRoles([$data['role']]);

            // Generar token
            $token = $user->createToken('test-token')->plainTextToken;
            file_put_contents($tokensPath, strtoupper($data['role']) . ": {$token}\n", FILE_APPEND);
        }

        $this->command->info('✅ Usuarios, roles y tokens generados (storage/test-tokens.txt)');
    }
}
