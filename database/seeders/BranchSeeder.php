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
                'radio' => 500,
                'color' => '#3b82f6',
                'descripcion' => 'Sucursal principal ubicada en el corazón de Bucaramanga.',
                'capacidad_vehiculos' => 20,
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
                'radio' => 400,
                'color' => '#10b981',
                'descripcion' => 'Sucursal en zona comercial y de alto flujo.',
                'capacidad_vehiculos' => 15,
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
                'radio' => 450,
                'color' => '#f59e0b',
                'descripcion' => 'Sucursal orientada a entregas rápidas y domicilios.',
                'capacidad_vehiculos' => 12,
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
                'radio' => 500,
                'color' => '#ef4444',
                'descripcion' => 'Sucursal que cubre zonas rurales y extensas rutas.',
                'capacidad_vehiculos' => 10,
                'activa' => true,
            ],
        ]);
    }
}
