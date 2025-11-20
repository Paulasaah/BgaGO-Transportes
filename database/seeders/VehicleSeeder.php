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
        $branchesByName = Branch::pluck('id', 'nombre')->toArray();
        $driverUserIds = User::role('conductor')->pluck('id')->toArray();

        if (empty($branchesByName)) {
            $this->command->warn('⚠️ No hay sedes (Branch) para crear vehículos.');
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

        $totalVehicles = count($vehicles);

        // Definir cuántos vehículos serán de catálogo vs flota de domicilios
        $catalogCount = min(6, $totalVehicles); // primeros 6 para catálogo (si existen)

        // Asignación determinística placa -> sede por nombre
        $plateBranchMap = [
            'ABC-123' => 'Cabecera',
            'GHJ-321' => 'Cabecera',
            'QWE-654' => 'Cabecera',
            'XYZ-789' => 'Centro',
            'POI-852' => 'Centro',
            'LMN-456' => 'Floridablanca',
            'RTY-963' => 'Floridablanca',
            'VBN-357' => 'Cañaveral',
            'TGB-159' => 'Cañaveral',
            'UJK-741' => 'Cañaveral',
        ];

        foreach ($vehicles as $index => $v) {
            $branchName = $plateBranchMap[$v['placa']] ?? array_key_first($branchesByName);
            $branchId = $branchesByName[$branchName] ?? reset($branchesByName);

            // Catálogo: visibles, sin conductor obligatorio
            $isCatalog = $index < $catalogCount;

            $conductorId = null;
            if (!$isCatalog && !empty($driverUserIds)) {
                // Flota de domicilios: asignar un conductor activo
                $conductorId = $driverUserIds[array_rand($driverUserIds)];
            }

            Vehicle::updateOrCreate(
                ['placa' => $v['placa']], // clave única
                array_merge($v, [
                    // Asignar sedes en round-robin para garantizar al menos un vehículo por sede
                    'sede_id' => $branchId,
                    'conductor_id' => $conductorId,
                    'estado' => 'disponible',
                    'visible_catalogo' => $isCatalog,
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
