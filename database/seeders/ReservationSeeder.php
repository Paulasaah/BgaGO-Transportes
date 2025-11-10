<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Str;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $vehicles = Vehicle::limit(3)->get();
        $users = User::limit(3)->get();

        if ($branches->isEmpty() || $vehicles->isEmpty() || $users->isEmpty()) {
            $this->command->warn('⚠️ No hay suficientes datos en branches, vehicles o users para crear reservas.');
            return;
        }

        // ✅ Reserva 1: Centro → Cañaveral (activa)
        Reservation::create([
            'codigo' => 'RES-' . now()->format('Y') . '-001',
            'user_id' => $users[0]->id,
            'vehiculo_id' => $vehicles[0]->id,
            'conductor_id' => $users[1]->id ?? null,
            'sede_id' => $branches[0]->id,
            'tipo' => 'domicilio',
            'estado' => 'activa',
            'origen_direccion' => 'Carrera 19 #35-10, Centro',
            'origen_lat' => 7.125420,
            'origen_lng' => -73.119800,
            'destino_direccion' => 'Calle 48 #29-20, Cañaveral',
            'destino_lat' => 7.065200,
            'destino_lng' => -73.099200,
            'fecha_inicio' => now()->subMinutes(15),
            'fecha_fin' => now()->addMinutes(15),
            'monto' => 15000,
            'descuento' => 0,
            'monto_final' => 15000,
            'distancia_km' => 3,
            'duracion_minutos' => 30,
            'notas_cliente' => 'Entregar paquete pequeño en recepción.',
        ]);

        // ✅ Reserva 2: Cabecera → Floridablanca (confirmada)
        Reservation::create([
            'codigo' => 'RES-' . now()->format('Y') . '-002',
            'user_id' => $users[1]->id,
            'vehiculo_id' => $vehicles[1]->id,
            'conductor_id' => $users[2]->id ?? null,
            'sede_id' => $branches[1]->id ?? $branches->first()->id,
            'tipo' => 'domicilio',
            'estado' => 'confirmada',
            'origen_direccion' => 'Carrera 36 #48-15, Cabecera',
            'origen_lat' => 7.119200,
            'origen_lng' => -73.109700,
            'destino_direccion' => 'Calle 7 #8-30, Floridablanca',
            'destino_lat' => 7.061200,
            'destino_lng' => -73.090100,
            'fecha_inicio' => now()->addMinutes(10),
            'fecha_fin' => now()->addMinutes(40),
            'monto' => 22000,
            'descuento' => 2000,
            'monto_final' => 20000,
            'distancia_km' => 6,
            'duracion_minutos' => 30,
            'notas_cliente' => 'Entrega de alimentos, manejar con cuidado.',
        ]);

        // ✅ Reserva 3: Cacique → San Pío (pendiente)
        Reservation::create([
            'codigo' => 'RES-' . now()->format('Y') . '-003',
            'user_id' => $users[2]->id,
            'vehiculo_id' => $vehicles[2]->id,
            'conductor_id' => null,
            'sede_id' => $branches[2]->id ?? $branches->first()->id,
            'tipo' => 'reserva',
            'estado' => 'pendiente',
            'origen_direccion' => 'Centro Comercial Cacique',
            'origen_lat' => 7.094300,
            'origen_lng' => -73.105900,
            'destino_direccion' => 'Parque San Pío',
            'destino_lat' => 7.116800,
            'destino_lng' => -73.107500,
            'fecha_inicio' => now()->addHour(),
            'fecha_fin' => now()->addHours(2),
            'monto' => 10000,
            'descuento' => 0,
            'monto_final' => 10000,
            'distancia_km' => 2,
            'duracion_minutos' => 60,
            'notas_cliente' => 'Esperar en la entrada principal del centro comercial.',
        ]);

        $this->command->info('✅ Reservas de ejemplo creadas correctamente con estructura avanzada.');
    }
}
