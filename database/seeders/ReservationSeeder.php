<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Branch;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $vehicles = Vehicle::all();
        $branches = Branch::all();

        $reservations = [
            ['tipo' => 'reserva', 'estado' => 'confirmada'],
            ['tipo' => 'domicilio', 'estado' => 'pendiente'],
            ['tipo' => 'reserva', 'estado' => 'activa'],
            ['tipo' => 'reserva', 'estado' => 'completada'],
            ['tipo' => 'domicilio', 'estado' => 'cancelada'],
            ['tipo' => 'reserva', 'estado' => 'pendiente'],
            ['tipo' => 'reserva', 'estado' => 'activa'],
            ['tipo' => 'domicilio', 'estado' => 'confirmada'],
            ['tipo' => 'reserva', 'estado' => 'completada'],
            ['tipo' => 'reserva', 'estado' => 'pendiente'],
        ];

        foreach ($reservations as $i => $data) {
            $start = now()->subDays(rand(1, 10))->setTime(rand(7, 12), 0);
            $end = (clone $start)->addHours(2);

            Reservation::create([
                'codigo' => 'RES-2025-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'user_id' => $users->random()->id,
                'vehiculo_id' => $vehicles->random()->id,
                'conductor_id' => $users->random()->id,
                'sede_id' => $branches->random()->id,
                'tipo' => $data['tipo'],
                'estado' => $data['estado'],
                'origen_direccion' => 'Cra 27 #45-67, Bucaramanga',
                'origen_lat' => 7.1193,
                'origen_lng' => -73.1227,
                'destino_direccion' => 'Calle 30 #20-15, Floridablanca',
                'destino_lat' => 7.0657,
                'destino_lng' => -73.0863,
                'fecha_inicio' => $start,
                'fecha_fin' => $end,
                'monto' => 45000,
                'descuento' => 5000,
                'monto_final' => 40000,
                'notas_cliente' => 'Solicito casco adicional.',
                'notas_conductor' => 'Vehículo entregado en óptimas condiciones.',
                'distancia_km' => 12,
                'duracion_minutos' => 25,
                'calificacion_cliente' => rand(4, 5),
                'comentario_cliente' => 'Buen servicio.',
            ]);
        }
    }
}
