<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::insert([
            [
                'nombre' => 'Sucursal Cabecera',
                'direccion' => 'Cra 33 #45-12',
                'ciudad' => 'Bucaramanga',
                'telefono' => '607-1234567',
                'email' => 'cabecera@empresa.com',
                'latitud' => 7.1193,
                'longitud' => -73.1227,
                'activa' => true,
            ],
            [
                'nombre' => 'Sucursal Cañaveral',
                'direccion' => 'Calle 146 #22-256',
                'ciudad' => 'Floridablanca',
                'telefono' => '607-2345678',
                'email' => 'canaveral@empresa.com',
                'latitud' => 7.0741,
                'longitud' => -73.1023,
                'activa' => true,
            ],
            [
                'nombre' => 'Sucursal Floridablanca',
                'direccion' => 'Av. Cañaveral 22-10',
                'ciudad' => 'Floridablanca',
                'telefono' => '607-3456789',
                'email' => 'floridablanca@empresa.com',
                'latitud' => 7.0648,
                'longitud' => -73.0863,
                'activa' => true,
            ],
            [
                'nombre' => 'Sucursal Piedecuesta',
                'direccion' => 'Calle 10 #22-58',
                'ciudad' => 'Piedecuesta',
                'telefono' => '607-4567890',
                'email' => 'piedecuesta@empresa.com',
                'latitud' => 6.9871,
                'longitud' => -73.0495,
                'activa' => true,
            ],
        ]);
    }
}
