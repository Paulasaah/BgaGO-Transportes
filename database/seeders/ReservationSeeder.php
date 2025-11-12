<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Models\Branch;
use App\Models\User;
use App\Enums\ReservationType;
use App\Enums\ReservationStatus;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $vehicles = Vehicle::all();
        $clientes = User::role('cliente')->pluck('id')->toArray();
        $conductores = User::role('conductor')->pluck('id')->toArray();

        if ($branches->isEmpty() || $vehicles->isEmpty() || empty($clientes) || empty($conductores)) {
            $this->command->warn('⚠️ No hay suficientes datos en Branch, Vehicle o User para crear reservas.');
            return;
        }

        $reservations = [];

        // 🔹 Generar 120 reservas realistas distribuidas en los últimos 6 meses
        for ($i = 1; $i <= 120; $i++) {
            $vehiculo = $vehicles->random();
            $clienteId = $clientes[array_rand($clientes)];
            $conductorId = $conductores[array_rand($conductores)];
            $sede = $branches->random();

            // Fecha aleatoria (últimos 6 meses)
            $fechaInicio = Carbon::now()->subDays(rand(0, 180))->addHours(rand(6, 22));
            $duracion = rand(15, 180); // minutos
            $fechaFin = (clone $fechaInicio)->addMinutes($duracion);

            // Tipo y estado aleatorio
            $tipo = rand(0, 1) ? ReservationType::Reserva : ReservationType::Domicilio;
            $estados = [
                ReservationStatus::Pendiente,
                ReservationStatus::Activa,
                ReservationStatus::Confirmada,
                ReservationStatus::Completada,
                ReservationStatus::Cancelada,
            ];
            $estado = $estados[array_rand($estados)];

            // Cálculos
            $distanciaKm = rand(2, 15);
            $monto = $distanciaKm * rand(2500, 3500);
            $descuento = rand(0, 3) ? 0 : rand(1000, 3000);
            $montoFinal = $monto - $descuento;

            // Direcciones simuladas por sede
            $direcciones = [
                'Centro' => 'Carrera 19 #35-10, Centro',
                'Cabecera' => 'Calle 36 #48-15, Cabecera',
                'Floridablanca' => 'Carrera 7 #8-30, Floridablanca',
                'Girón' => 'Carrera 23 #20-15, Girón',
            ];
            $origen = $direcciones[array_rand($direcciones)];
            $destino = $direcciones[array_rand($direcciones)];

            $reservations[] = [
                'codigo' => 'RES-' . now()->format('Y') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'user_id' => $clienteId,
                'vehiculo_id' => $vehiculo->id,
                'conductor_id' => $conductorId,
                'sede_id' => $sede->id,
                'tipo' => $tipo,
                'estado' => $estado,
                'origen_direccion' => $origen,
                'origen_lat' => 7.12 + mt_rand(-100, 100) / 1000,
                'origen_lng' => -73.12 + mt_rand(-100, 100) / 1000,
                'destino_direccion' => $destino,
                'destino_lat' => 7.10 + mt_rand(-100, 100) / 1000,
                'destino_lng' => -73.10 + mt_rand(-100, 100) / 1000,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'monto' => $monto,
                'descuento' => $descuento,
                'monto_final' => $montoFinal,
                'distancia_km' => $distanciaKm,
                'duracion_minutos' => $duracion,
                'notas_cliente' => $tipo === ReservationType::Domicilio
                    ? 'Entrega de paquete o envío particular.'
                    : 'Reserva estándar de vehículo para traslado urbano.',
                'created_at' => $fechaInicio,
                'updated_at' => $fechaFin,
            ];
        }

        foreach ($reservations as $data) {
            Reservation::updateOrCreate(['codigo' => $data['codigo']], $data);
        }

        $this->command->info('✅ 120 reservas creadas con variedad de fechas, estados y tipos.');
    }
}
