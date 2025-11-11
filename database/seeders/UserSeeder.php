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
        // 🧱 1. Crear roles base si no existen
        $roles = ['admin', 'cliente', 'conductor'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // 👤 2. Crear usuarios base
        $users = [
            [
                'name' => 'Carlos Gómez',
                'email' => 'carlos@example.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Laura Rojas',
                'email' => 'laura@example.com',
                'role' => 'cliente',
            ],
            [
                'name' => 'Andrés Díaz',
                'email' => 'andres@example.com',
                'role' => 'conductor',
            ],
        ];

        // 📂 3. Limpiar archivo previo de tokens
        $tokensPath = storage_path('test-tokens.txt');
        if (file_exists($tokensPath)) {
            unlink($tokensPath);
        }

        // 🔑 4. Crear usuarios y generar tokens
        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'updated_at' => now(),
                ]
            );

            // Asignar o sincronizar rol
            $user->syncRoles([$data['role']]);

            // Generar token de prueba
            $token = $user->createToken('test-token')->plainTextToken;
            file_put_contents($tokensPath, strtoupper($data['role']) . ": {$token}\n", FILE_APPEND);
        }

        $this->command->info('✅ Usuarios, roles y tokens generados correctamente (storage/test-tokens.txt)');
    }
}
