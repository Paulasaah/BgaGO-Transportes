<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\Branch;
use App\Models\User;
use App\Enums\VehicleType;
use App\Enums\VehicleStatus;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $branchIds = Branch::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();

        if (empty($branchIds) || empty($userIds)) {
            $this->command->warn('⚠️ No hay datos suficientes en Branch o User para crear vehículos.');
            return;
        }

        $vehicles = [
            ['placa' => 'ABC-123', 'marca' => 'Yamaha', 'modelo' => 'NMAX', 'year' => 2021, 'tipo' => VehicleType::Moto->value, 'color' => 'Negro'],
            ['placa' => 'XYZ-789', 'marca' => 'Honda', 'modelo' => 'Click 150', 'year' => 2020, 'tipo' => VehicleType::Scooter->value, 'color' => 'Rojo'],
            ['placa' => 'LMN-456', 'marca' => 'Suzuki', 'modelo' => 'Address', 'year' => 2022, 'tipo' => VehicleType::Moto->value, 'color' => 'Blanco'],
            ['placa' => 'GHJ-321', 'marca' => 'Kymco', 'modelo' => 'Agility', 'year' => 2023, 'tipo' => VehicleType::Scooter->value, 'color' => 'Azul'],
            ['placa' => 'QWE-654', 'marca' => 'Auteco', 'modelo' => 'Eco', 'year' => 2021, 'tipo' => VehicleType::Bicicleta->value, 'color' => 'Verde'],
            ['placa' => 'POI-852', 'marca' => 'Yamaha', 'modelo' => 'FZ 25', 'year' => 2019, 'tipo' => VehicleType::Moto->value, 'color' => 'Gris'],
            ['placa' => 'RTY-963', 'marca' => 'SkatePro', 'modelo' => 'Street X', 'year' => 2023, 'tipo' => VehicleType::Scooter->value, 'color' => 'Negro'],
            ['placa' => 'UJK-741', 'marca' => 'Kymco', 'modelo' => 'Like', 'year' => 2022, 'tipo' => VehicleType::Scooter->value, 'color' => 'Rojo'],
            ['placa' => 'VBN-357', 'marca' => 'Auteco', 'modelo' => 'Starker', 'year' => 2023, 'tipo' => VehicleType::Bicicleta->value, 'color' => 'Azul'],
            ['placa' => 'TGB-159', 'marca' => 'Suzuki', 'modelo' => 'Gixxer', 'year' => 2021, 'tipo' => VehicleType::Moto->value, 'color' => 'Blanco'],
        ];

        foreach ($vehicles as $v) {
            $tipoEnum = VehicleType::from($v['tipo']);
            Vehicle::updateOrCreate(
                ['placa' => $v['placa']], // clave única
                array_merge($v, [
                    'sede_id' => $branchIds[array_rand($branchIds)],
                    'conductor_id' => $userIds[array_rand($userIds)],
                    'estado' => VehicleStatus::Disponible->value,
                    'visible_catalogo' => true,
                    'precio_hora' => $tipoEnum->baseHourlyRate(),
                    'precio_dia' => $tipoEnum->baseDailyRate(),
                    'imagen_principal' => 'https://via.placeholder.com/400x300',
                    'descripcion' => 'Vehículo en excelentes condiciones para servicio urbano.',
                ])
            );
        }

        $this->command->info('✅ Vehículos creados o actualizados correctamente.');
    }
}
