<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Carlos Gómez', 'email' => 'carlos@example.com'],
            ['name' => 'Laura Rojas', 'email' => 'laura@example.com'],
            ['name' => 'Andrés Díaz', 'email' => 'andres@example.com'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('✅ Usuarios base creados o actualizados correctamente.');
    }
}
