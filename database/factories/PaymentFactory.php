<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\User;
use App\Models\Reservation;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'codigo_transaccion' => 'TXN-' . date('Ymd') . '-' . strtoupper($this->faker->bothify('??????')),
            'user_id' => User::factory(),
            'reserva_id' => Reservation::factory(),
            'monto' => $this->faker->randomFloat(2, 50, 500),
            'metodo_pago' => 'mercadopago', // Mercado Pago como método principal
            'estado' => PaymentStatus::Pendiente,
            'referencia_externa' => 'MP-' . $this->faker->numberBetween(1000000000, 9999999999),
        ];
    }

    public function aprobado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => PaymentStatus::Aprobado,
            'fecha_aprobacion' => now(),
        ]);
    }

    public function rechazado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => PaymentStatus::Rechazado,
            'motivo_rechazo' => $this->faker->sentence(),
        ]);
    }

    public function pendiente(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => PaymentStatus::Pendiente,
        ]);
    }
}
