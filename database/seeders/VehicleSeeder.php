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
        $userIds = User::pluck('id')->toArray();

        if (empty($branchIds) || empty($userIds)) {
            $this->command->warn('⚠️ No hay datos suficientes en Branch o User para crear vehículos.');
            return;
        }

        $vehicles = [
            ['placa' => 'ABC-123', 'marca' => 'Yamaha', 'modelo' => 'NMAX', 'year' => 2021, 'tipo' => 'moto', 'color' => 'Negro'],
            ['placa' => 'XYZ-789', 'marca' => 'Honda', 'modelo' => 'Click 150', 'year' => 2020, 'tipo' => 'moto', 'color' => 'Rojo'],
            ['placa' => 'LMN-456', 'marca' => 'Suzuki', 'modelo' => 'Address', 'year' => 2022, 'tipo' => 'scooter', 'color' => 'Blanco'],
            ['placa' => 'GHJ-321', 'marca' => 'Kymco', 'modelo' => 'Agility', 'year' => 2023, 'tipo' => 'moto', 'color' => 'Azul'],
            ['placa' => 'QWE-654', 'marca' => 'Auteco', 'modelo' => 'Eco', 'year' => 2021, 'tipo' => 'bicicleta', 'color' => 'Verde'],
            ['placa' => 'POI-852', 'marca' => 'Yamaha', 'modelo' => 'FZ 25', 'year' => 2019, 'tipo' => 'moto', 'color' => 'Gris'],
            ['placa' => 'RTY-963', 'marca' => 'Honda', 'modelo' => 'Wave', 'year' => 2020, 'tipo' => 'moto', 'color' => 'Negro'],
            ['placa' => 'UJK-741', 'marca' => 'Kymco', 'modelo' => 'Like', 'year' => 2022, 'tipo' => 'scooter', 'color' => 'Rojo'],
            ['placa' => 'VBN-357', 'marca' => 'Auteco', 'modelo' => 'Starker', 'year' => 2023, 'tipo' => 'bicicleta', 'color' => 'Azul'],
            ['placa' => 'TGB-159', 'marca' => 'Suzuki', 'modelo' => 'Gixxer', 'year' => 2021, 'tipo' => 'moto', 'color' => 'Blanco'],
        ];

        foreach ($vehicles as $v) {
            Vehicle::updateOrCreate(
                ['placa' => $v['placa']], // clave única
                array_merge($v, [
                    'sede_id' => $branchIds[array_rand($branchIds)],
                    'conductor_id' => $userIds[array_rand($userIds)],
                    'estado' => 'disponible',
                    'visible_catalogo' => true,
                    'precio_hora' => 15000,
                    'precio_dia' => 60000,
                    'imagen_principal' => 'https://via.placeholder.com/400x300',
                    'descripcion' => 'Vehículo en excelentes condiciones para servicio urbano.',
                ])
            );
        }

        $this->command->info('✅ Vehículos creados o actualizados correctamente.');
    }
}
