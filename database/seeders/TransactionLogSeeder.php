<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionLog;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;

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

        $acciones = ['creación', 'aprobación', 'reembolso', 'error'];
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'Mozilla/5.0 (Linux; Android 12)',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)',
        ];

        $totalLogs = 0;

        foreach ($payments as $payment) {
            // Generar entre 2 y 4 logs por pago para simular su ciclo de vida
            $numLogs = rand(2, 4);
            $fechaBase = Carbon::now()->subDays(rand(0, 90));

            for ($i = 0; $i < $numLogs; $i++) {
                $accion = $acciones[array_rand($acciones)];
                $usuario = $users->random();

                $descripcion = match ($accion) {
                    'creación' => "Se registró el pago con código {$payment->codigo_transaccion}.",
                    'aprobación' => "El pago {$payment->codigo_transaccion} fue aprobado correctamente.",
                    'reembolso' => "Se procesó un reembolso parcial del pago {$payment->codigo_transaccion}.",
                    'error' => "Error en el intento de cobro para la transacción {$payment->codigo_transaccion}.",
                };

                $datos = [
                    'metodo' => $payment->metodo_pago,
                    'monto' => $payment->monto,
                    'estado' => $payment->estado,
                    'gateway' => fake()->randomElement(['PayU', 'Stripe', 'Wompi']),
                    'dispositivo' => fake()->randomElement(['web', 'mobile']),
                    'resultado' => $accion === 'error' ? 'fallido' : 'exitoso',
                    'referencia_externa' => $payment->referencia_externa ?? 'N/A',
                ];

                TransactionLog::create([
                    'payment_id' => $payment->id,
                    'user_id' => $usuario->id,
                    'accion' => $accion,
                    'descripcion' => $descripcion,
                    'datos' => $datos,
                    'ip' => fake()->ipv4(),
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'created_at' => (clone $fechaBase)->addMinutes($i * 10),
                    'updated_at' => (clone $fechaBase)->addMinutes($i * 15),
                ]);

                $totalLogs++;
            }
        }

        $this->command->info("✅ {$totalLogs} registros de transacciones creados exitosamente con acciones variadas.");
    }
}
