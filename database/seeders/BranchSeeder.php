<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'nombre' => 'Cabecera',
                'direccion' => 'Calle 42 #33-44, Cabecera del Llano',
                'lat' => 7.1193,
                'lon' => -73.1227,
                'radio' => 500, // Radio en metros
                'color' => '#3b82f6',
                'descripcion' => 'Sede principal - Zona Cabecera',
                'capacidad_vehiculos' => 20,
            ],
            [
                'nombre' => 'Centro',
                'direccion' => 'Carrera 19 #35-20, Centro',
                'lat' => 7.1254,
                'lon' => -73.1198,
                'radio' => 400,
                'color' => '#10b981',
                'descripcion' => 'Sede Centro - Alta demanda',
                'capacidad_vehiculos' => 15,
            ],
            [
                'nombre' => 'Floridablanca',
                'direccion' => 'Calle 6 #12-45, Floridablanca',
                'lat' => 7.0621,
                'lon' => -73.0873,
                'radio' => 450,
                'color' => '#f59e0b',
                'descripcion' => 'Sede Floridablanca',
                'capacidad_vehiculos' => 18,
            ],
            [
                'nombre' => 'Cañaveral',
                'direccion' => 'Carrera 27 #54-32, Cañaveral',
                'lat' => 7.0893,
                'lon' => -73.1074,
                'radio' => 350,
                'color' => '#8b5cf6',
                'descripcion' => 'Sede Cañaveral - Zona residencial',
                'capacidad_vehiculos' => 12,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(
                ['nombre' => $branch['nombre']],
                $branch
            );
        }

        $this->command->info('✅ Sedes creadas correctamente');
    }
}
