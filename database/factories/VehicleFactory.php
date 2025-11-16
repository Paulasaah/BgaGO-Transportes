<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\Branch;
use App\Enums\VehicleStatus;
use App\Enums\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        // Solo usar tipos válidos según la migración
        $tiposValidos = [VehicleType::Moto, VehicleType::Bicicleta, VehicleType::Scooter];
        $tipo = $this->faker->randomElement($tiposValidos);
        
        // Datos realistas según el tipo de vehículo
        $vehiculos = [
            VehicleType::Moto->value => [
                'marca' => 'Yadea',
                'modelo' => 'G5',
                'color' => 'Negro',
            ],
            VehicleType::Bicicleta->value => [
                'marca' => 'Trek',
                'modelo' => 'FX+ 2',
                'color' => 'Azul',
            ],
            VehicleType::Scooter->value => [
                'marca' => 'Xiaomi',
                'modelo' => 'Mi Electric Scooter',
                'color' => 'Blanco',
            ],
            VehicleType::Patineta->value => [
                'marca' => 'Segway',
                'modelo' => 'Ninebot',
                'color' => 'Gris',
            ],
        ];
        
        $datos = $vehiculos[$tipo->value];
        
        return [
            'placa' => strtoupper($this->faker->bothify('???###')),
            'marca' => $datos['marca'],
            'modelo' => $datos['modelo'],
            'year' => $this->faker->numberBetween(2022, 2024),
            'color' => $datos['color'],
            'tipo' => $tipo,
            'estado' => VehicleStatus::Disponible,
            'sede_id' => Branch::factory(),
            'precio_hora' => $tipo->baseHourlyRate(),
            'precio_dia' => $tipo->baseDailyRate(),
            'visible_catalogo' => true,
            'descripcion' => 'Vehículo eléctrico ' . $datos['marca'] . ' ' . $datos['modelo'],
        ];
    }

    public function disponible(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => VehicleStatus::Disponible,
        ]);
    }

    public function ocupado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => VehicleStatus::Ocupado,
        ]);
    }

    public function mantenimiento(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => VehicleStatus::Mantenimiento,
        ]);
    }
}
