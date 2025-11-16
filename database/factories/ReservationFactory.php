<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Branch;
use App\Enums\ReservationType;
use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        $fechaInicio = $this->faker->dateTimeBetween('+1 day', '+7 days');
        $fechaFin = (clone $fechaInicio)->modify('+2 hours');

        return [
            'codigo' => 'RES-' . date('Y') . '-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'user_id' => User::factory(),
            'vehiculo_id' => Vehicle::factory(),
            'sede_id' => Branch::factory(),
            'tipo' => ReservationType::Reserva,
            'estado' => ReservationStatus::Pendiente,
            'origen_direccion' => 'Carrera 27 #34-52, Bucaramanga',
            'origen_lat' => 7.1254,
            'origen_lng' => -73.1198,
            'destino_direccion' => 'Calle 45 #28-73, Bucaramanga',
            'destino_lat' => 7.1193,
            'destino_lng' => -73.1227,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'monto' => 100.00,
            'descuento' => 0,
            'monto_final' => 100.00,
            'duracion_minutos' => 120,
            'notas_cliente' => $this->faker->optional()->sentence(),
        ];
    }

    public function pendiente(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => ReservationStatus::Pendiente,
        ]);
    }

    public function confirmada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => ReservationStatus::Confirmada,
            'fecha_confirmacion' => now(),
        ]);
    }

    public function activa(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => ReservationStatus::Activa,
            'fecha_confirmacion' => now()->subHours(2),
            'fecha_inicio_real' => now()->subHour(),
        ]);
    }

    public function completada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => ReservationStatus::Completada,
            'fecha_confirmacion' => now()->subDays(2),
            'fecha_inicio_real' => now()->subDays(1),
            'fecha_fin_real' => now()->subHours(22),
        ]);
    }

    public function cancelada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => ReservationStatus::Cancelada,
            'motivo_cancelacion' => $this->faker->sentence(),
            'fecha_cancelacion' => now(),
        ]);
    }

    public function domicilio(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => ReservationType::Domicilio,
        ]);
    }
}
