<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        // Datos realistas de Bucaramanga
        $sedes = [
            ['nombre' => 'Sede Centro', 'direccion' => 'Carrera 19 #34-45, Centro', 'lat' => 7.1254, 'lon' => -73.1198],
            ['nombre' => 'Sede Cabecera', 'direccion' => 'Calle 42 #27-85, Cabecera', 'lat' => 7.1193, 'lon' => -73.1227],
            ['nombre' => 'Sede Cañaveral', 'direccion' => 'Carrera 27 #123-45, Cañaveral', 'lat' => 7.0897, 'lon' => -73.1067],
            ['nombre' => 'Sede Provenza', 'direccion' => 'Calle 105 #23-67, Provenza', 'lat' => 7.1089, 'lon' => -73.1234],
        ];
        
        $sede = $this->faker->randomElement($sedes);
        
        return [
            'nombre' => $sede['nombre'],
            'direccion' => $sede['direccion'],
            'lat' => $sede['lat'],
            'lon' => $sede['lon'],
            'radio' => 500,
            'color' => '#3b82f6',
            'descripcion' => 'Sede de BgaGO en ' . explode(' ', $sede['nombre'])[1],
            'capacidad_vehiculos' => 15,
        ];
    }

    public function activo(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => true,
        ]);
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }
}
