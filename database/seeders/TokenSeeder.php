<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\File;

class TokenSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔐 Generando tokens personales para pruebas...');

        // Asegurar usuarios base
        $users = [
            ['email' => 'carlos@example.com', 'role' => 'admin'],
            ['email' => 'laura@example.com', 'role' => 'cliente'],
            ['email' => 'andres@example.com', 'role' => 'conductor'],
        ];

        $tokens = [];

        foreach ($users as $u) {
            $user = User::where('email', $u['email'])->first();

            if (!$user) {
                $this->command->warn("⚠️ Usuario {$u['email']} no existe. Ejecuta primero UserSeeder.");
                continue;
            }

            // Asignar rol si no lo tiene
            if (!$user->hasRole($u['role'])) {
                $user->assignRole($u['role']);
            }

            // Crear token
            $token = $user->createToken("{$u['role']}-test-token")->plainTextToken;
            $tokens[strtoupper($u['role'])] = $token;

            $this->command->info("✅ Token generado para {$u['role']}: {$u['email']}");
        }

        // Guardar en archivo
        $output = "";
        foreach ($tokens as $role => $token) {
            $output .= "{$role}: {$token}\n";
        }

        $path = storage_path('test-tokens.txt');
        File::put($path, $output);

        $this->command->info("📄 Tokens guardados en: {$path}");
    }
}
