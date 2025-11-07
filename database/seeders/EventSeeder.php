<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Reservation;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $vehicles = Vehicle::all();
        $reservations = Reservation::all();

        $eventTypes = [
            'servicio_iniciado',
            'servicio_completado',
            'mantenimiento_programado',
            'mantenimiento_completado',
            'pago_recibido',
            'alerta_generada',
            'vehiculo_asignado',
            'usuario_registrado',
            'conductor_disponible',
            'conductor_no_disponible',
        ];

        foreach ($eventTypes as $i => $type) {
            Event::create([
                'tipo' => $type,
                'user_id' => $users->random()->id,
                'vehiculo_id' => $vehicles->random()->id,
                'reserva_id' => $reservations->random()->id,
                'titulo' => ucfirst(str_replace('_', ' ', $type)),
                'descripcion' => 'Evento del sistema correspondiente a ' . $type,
                'datos' => ['origen' => 'sistema', 'prioridad' => 'media'],
                'ip' => '192.168.1.' . rand(2, 254),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            ]);
        }
    }
}
