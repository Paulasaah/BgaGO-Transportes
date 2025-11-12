<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $reservations = Reservation::all();
        $users = User::all();

        if ($reservations->isEmpty()) {
            $this->command->warn('⚠️ No hay reservas disponibles. Se omite PaymentSeeder.');
            return;
        }

        $metodos = ['efectivo', 'tarjeta', 'transferencia'];
        $estados = ['pendiente', 'aprobado', 'rechazado', 'reembolsado'];

        $count = 0;

        foreach ($reservations as $reservation) {
            $estado = match (rand(1, 10)) {
                1, 2 => 'pendiente',      // 20%
                3, 4 => 'rechazado',      // 20%
                5 => 'reembolsado',       // 10%
                default => 'aprobado',    // 50%
            };

            $metodo = $metodos[array_rand($metodos)];
            $montoBase = $reservation->monto_final ?? rand(15000, 70000);

            $paymentData = [
                'codigo_transaccion' => 'TX-' . strtoupper(bin2hex(random_bytes(4))),
                'user_id' => $users->random()->id,
                'reserva_id' => $reservation->id,
                'metodo_pago' => $metodo,
                'monto' => $montoBase,
                'estado' => $estado,
                'referencia_externa' => $estado === 'aprobado'
                    ? strtoupper($metodo) . '-' . rand(10000, 99999)
                    : null,
                'datos_transaccion' => json_encode([
                    'gateway' => match ($metodo) {
                        'tarjeta' => 'Stripe',
                        'paypal' => 'PayPal',
                        default => 'PayU',
                    },
                    'currency' => 'COP',
                    'device' => fake()->randomElement(['mobile', 'desktop']),
                ]),
                'fecha_aprobacion' => $estado === 'aprobado'
                    ? Carbon::now()->subDays(rand(1, 90))->setTime(rand(6, 22), rand(0, 59))
                    : null,
                'created_at' => $reservation->created_at ?? now()->subDays(rand(10, 90)),
                'updated_at' => now(),
            ];

            Payment::updateOrCreate(['reserva_id' => $reservation->id], $paymentData);
            $count++;
        }

        $this->command->info("✅ Pagos generados: {$count} (con métodos y estados variados).");
    }
}
