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

        foreach ($reservations as $reservation) {
            Payment::updateOrCreate(
                ['reserva_id' => $reservation->id], // clave única
                [
                    'codigo_transaccion' => 'TX-' . strtoupper(uniqid()),
                    'user_id' => $users->random()->id,
                    'metodo_pago' => 'tarjeta',
                    'monto' => $reservation->monto_final ?? 0,
                    'estado' => 'aprobado',
                    'referencia_externa' => 'STRIPE-' . rand(10000, 99999),
                    'datos_transaccion' => json_encode(['gateway' => 'Stripe', 'currency' => 'COP']),
                    'fecha_aprobacion' => now()->subDays(rand(1, 10)),
                ]
            );
        }

        $this->command->info('✅ Pagos creados o actualizados correctamente.');
    }
}
