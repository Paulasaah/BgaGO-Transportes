<?php

namespace Database\Factories;

use App\Models\Delivery;
use App\Models\Reservation;
use App\Enums\DeliveryStatus;
use App\Enums\DeliveryType;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryFactory extends Factory
{
    protected $model = Delivery::class;

    public function definition(): array
    {
        return [
            'reserva_id' => Reservation::factory(),
            'tipo' => DeliveryType::Paquete,
            'estado' => DeliveryStatus::Pendiente,
            'descripcion_paquete' => $this->faker->sentence(),
            'peso_kg' => $this->faker->randomFloat(2, 0.5, 50),
            'dimensiones' => json_encode([
                'largo' => $this->faker->numberBetween(10, 100),
                'ancho' => $this->faker->numberBetween(10, 100),
                'alto' => $this->faker->numberBetween(10, 100),
            ]),
        ];
    }

    public function pendiente(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => DeliveryStatus::Pendiente,
        ]);
    }

    public function asignado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => DeliveryStatus::Asignado,
        ]);
    }

    public function enCurso(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => DeliveryStatus::EnCurso,
            'fecha_inicio_real' => now()->subMinutes(30),
        ]);
    }

    public function entregado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => DeliveryStatus::Entregado,
            'fecha_inicio_real' => now()->subHours(2),
            'fecha_entrega_real' => now()->subHour(),
            'firma_receptor' => $this->faker->name(),
        ]);
    }

    public function vehiculo(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => DeliveryType::Vehiculo,
            'descripcion_paquete' => null,
            'peso_kg' => null,
        ]);
    }

    public function paquete(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => DeliveryType::Paquete,
        ]);
    }
}
