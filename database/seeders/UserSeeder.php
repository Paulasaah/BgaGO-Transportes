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
            ['name' => 'Ana Torres', 'email' => 'ana@example.com'],
            ['name' => 'Juan Martínez', 'email' => 'juan@example.com'],
            ['name' => 'María Pérez', 'email' => 'maria@example.com'],
            ['name' => 'Sofía Ramírez', 'email' => 'sofia@example.com'],
            ['name' => 'Andrés López', 'email' => 'andres@example.com'],
            ['name' => 'Laura Castillo', 'email' => 'laura@example.com'],
            ['name' => 'Felipe Díaz', 'email' => 'felipe@example.com'],
            ['name' => 'Camila Herrera', 'email' => 'camila@example.com'],
            ['name' => 'Daniel Rojas', 'email' => 'daniel@example.com'],
        ];

        foreach ($users as $u) {
            User::create([
                'name' => $u['name'],
                'email' => $u['email'],
                'password' => Hash::make('12345678'),
            ]);
        }
    }
}
