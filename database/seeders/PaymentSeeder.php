<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;

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

        foreach ($reservations as $index => $reservation) {
            // Alternar entre pagos aprobados y pendientes
            $estado = ($index === 0) ? 'pendiente' : (($index % 2 === 0) ? 'pendiente' : 'aprobado');

            Payment::updateOrCreate(
                ['reserva_id' => $reservation->id],
                [
                    'codigo_transaccion' => 'TX-' . strtoupper(uniqid()),
                    'user_id' => $users->random()->id,
                    'metodo_pago' => 'tarjeta',
                    'monto' => $reservation->monto_final ?? 0,
                    'estado' => $estado,
                    'referencia_externa' => $estado === 'aprobado'
                        ? 'STRIPE-' . rand(10000, 99999)
                        : null,
                    'datos_transaccion' => json_encode([
                        'gateway' => 'Stripe',
                        'currency' => 'COP',
                    ]),
                    'fecha_aprobacion' => $estado === 'aprobado' ? now()->subDays(rand(1, 10)) : null,
                ]
            );
        }

        $this->command->info('✅ Pagos creados o actualizados correctamente (pendientes y aprobados).');
    }
}
