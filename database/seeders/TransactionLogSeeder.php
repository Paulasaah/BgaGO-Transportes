<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionLog;
use App\Models\Payment;
use App\Models\User;

class TransactionLogSeeder extends Seeder
{
    public function run(): void
    {
        $payments = Payment::all();
        $users = User::all();

        if ($payments->isEmpty()) {
            $this->command->warn('⚠️ No hay pagos disponibles para registrar logs.');
            return;
        }

        foreach ($payments as $payment) {
            TransactionLog::updateOrCreate(
                [
                    'payment_id' => $payment->id,
                    'accion' => 'creación',
                ],
                [
                    'user_id' => $users->random()->id,
                    'descripcion' => 'Se registró el pago con código ' . $payment->codigo_transaccion,
                    'datos' => [
                        'metodo' => $payment->metodo_pago,
                        'monto' => $payment->monto,
                        'estado' => $payment->estado,
                    ],
                    'ip' => fake()->ipv4(),
                    'user_agent' => fake()->userAgent(),
                ]
            );
        }

        $this->command->info('✅ Logs de transacciones creados o actualizados correctamente.');
    }
}
