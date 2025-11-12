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

        if ($users->isEmpty() || $vehicles->isEmpty()) {
            $this->command->warn('⚠️ No hay usuarios o vehículos disponibles. Se omite EventSeeder.');
            return;
        }

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

        foreach ($eventTypes as $type) {
            Event::updateOrCreate(
                ['tipo' => $type],
                [
                    'user_id' => $users->random()->id,
                    'vehiculo_id' => $vehicles->random()->id,
                    'reserva_id' => $reservations->isNotEmpty() ? $reservations->random()->id : null,
                    'titulo' => ucfirst(str_replace('_', ' ', $type)),
                    'descripcion' => 'Evento del sistema correspondiente a ' . $type,
                    'datos' => ['origen' => 'sistema', 'prioridad' => 'media'],
                    'ip' => '192.168.1.' . rand(2, 254),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                ]
            );
        }

        $this->command->info('✅ Eventos creados o actualizados correctamente.');
    }
}
