<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\Branch;
use App\Models\User;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $branchIds = Branch::pluck('id')->toArray();
        $userIds = User::role('conductor')->pluck('id')->toArray(); // solo conductores

        if (empty($branchIds) || empty($userIds)) {
            $this->command->warn('⚠️ No hay datos suficientes en Branch o User para crear vehículos.');
            return;
        }

        // Catálogo base de modelos según tipo
        $modelos = [
            // 🔹 Motos
            ['marca' => 'Yamaha', 'modelo' => 'NMAX', 'tipo' => 'moto', 'color' => 'Negro'],
            ['marca' => 'Honda', 'modelo' => 'Click 150', 'tipo' => 'moto', 'color' => 'Rojo'],
            ['marca' => 'Suzuki', 'modelo' => 'Gixxer', 'tipo' => 'moto', 'color' => 'Azul'],
            ['marca' => 'AKT', 'modelo' => 'NKD 125', 'tipo' => 'moto', 'color' => 'Gris'],
            ['marca' => 'Victory', 'modelo' => 'Venom 200', 'tipo' => 'moto', 'color' => 'Blanco'],

            // 🔹 Scooters
            ['marca' => 'Kymco', 'modelo' => 'Agility', 'tipo' => 'scooter', 'color' => 'Negro'],
            ['marca' => 'NIU', 'modelo' => 'MQi GT Evo', 'tipo' => 'scooter', 'color' => 'Rojo'],
            ['marca' => 'Auteco', 'modelo' => 'Stärker', 'tipo' => 'scooter', 'color' => 'Azul'],
            ['marca' => 'Segway', 'modelo' => 'Ninebot', 'tipo' => 'scooter', 'color' => 'Blanco'],
            ['marca' => 'Super Soco', 'modelo' => 'CPx', 'tipo' => 'scooter', 'color' => 'Gris'],

            // 🔹 Bicicletas eléctricas
            ['marca' => 'Auteco', 'modelo' => 'Eco 2.0', 'tipo' => 'bicicleta', 'color' => 'Verde'],
            ['marca' => 'Giant', 'modelo' => 'Escape 3', 'tipo' => 'bicicleta', 'color' => 'Azul'],
            ['marca' => 'Trek', 'modelo' => 'FX 2', 'tipo' => 'bicicleta', 'color' => 'Negro'],
            ['marca' => 'Totem', 'modelo' => 'Wave', 'tipo' => 'bicicleta', 'color' => 'Gris'],
            ['marca' => 'BMC', 'modelo' => 'City E', 'tipo' => 'bicicleta', 'color' => 'Rojo'],
        ];

        $vehicles = [];
        $letras = range('A', 'Z');

        // 🔹 Crear 45 vehículos balanceados entre las sedes
        for ($i = 1; $i <= 45; $i++) {
            $modelo = $modelos[array_rand($modelos)];
            $placa = sprintf(
                "%s%s%s-%03d",
                $letras[array_rand($letras)],
                $letras[array_rand($letras)],
                $letras[array_rand($letras)],
                rand(100, 999)
            );

            $vehicles[] = [
                'placa' => $placa,
                'marca' => $modelo['marca'],
                'modelo' => $modelo['modelo'],
                'year' => rand(2019, 2024),
                'tipo' => $modelo['tipo'],
                'color' => $modelo['color'],
                'sede_id' => $branchIds[array_rand($branchIds)],
                'conductor_id' => $userIds[array_rand($userIds)],
                'estado' => (rand(1, 10) > 2) ? 'disponible' : 'mantenimiento', // 20% mantenimiento
                'visible_catalogo' => true,
                'precio_hora' => match ($modelo['tipo']) {
                    'bicicleta' => rand(8000, 12000),
                    'scooter' => rand(13000, 16000),
                    default => rand(15000, 18000),
                },
                'precio_dia' => match ($modelo['tipo']) {
                    'bicicleta' => rand(35000, 45000),
                    'scooter' => rand(50000, 70000),
                    default => rand(60000, 85000),
                },
                'imagen_principal' => 'https://via.placeholder.com/400x300?text=' . urlencode($modelo['marca'] . ' ' . $modelo['modelo']),
                'descripcion' => "Vehículo {$modelo['tipo']} {$modelo['marca']} {$modelo['modelo']} disponible para movilidad urbana eficiente y ecológica.",
                'created_at' => now()->subDays(rand(0, 120)),
                'updated_at' => now(),
            ];
        }

        foreach ($vehicles as $v) {
            Vehicle::updateOrCreate(
                ['placa' => $v['placa']],
                $v
            );
        }

        $this->command->info('✅ 45 vehículos creados correctamente (motos, scooters y bicicletas).');
    }
}
