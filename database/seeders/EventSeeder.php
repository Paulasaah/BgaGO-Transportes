<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Reservation;
use Carbon\Carbon;

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

        $tipos = [
            'servicio_iniciado',
            'servicio_completado',
            'servicio_cancelado',
            'conductor_disponible',
            'conductor_no_disponible',
            'vehiculo_asignado',
            'mantenimiento_programado',
            'mantenimiento_completado',
            'pago_recibido',
            'usuario_registrado',
            'alerta_generada',
            'otro'
        ];

        $totalEventos = rand(80, 120);
        $eventos = [];

        for ($i = 1; $i <= $totalEventos; $i++) {
            $tipo = $tipos[array_rand($tipos)];
            $usuario = $users->random();
            $vehiculo = $vehicles->random();
            $reserva = $reservations->isNotEmpty() ? $reservations->random() : null;
            $fecha = Carbon::now()->subDays(rand(0, 90))->addMinutes(rand(0, 1440));

            // Título y descripción coherente con el tipo
            $titulo = match ($tipo) {
                'servicio_iniciado' => "Servicio iniciado por {$usuario->name}",
                'servicio_completado' => "Servicio completado exitosamente",
                'servicio_cancelado' => "Servicio cancelado por el usuario",
                'conductor_disponible' => "{$usuario->name} ahora está disponible",
                'conductor_no_disponible' => "{$usuario->name} se desconectó del servicio",
                'vehiculo_asignado' => "Vehículo {$vehiculo->placa} asignado correctamente",
                'mantenimiento_programado' => "Mantenimiento programado para {$vehiculo->placa}",
                'mantenimiento_completado' => "Mantenimiento completado: {$vehiculo->placa}",
                'pago_recibido' => "Pago recibido de {$usuario->name}",
                'usuario_registrado' => "Nuevo usuario registrado: {$usuario->name}",
                'alerta_generada' => "⚠️ Alerta generada en el sistema",
                default => "Evento del sistema registrado",
            };

            $descripcion = match ($tipo) {
                'servicio_iniciado' => "El usuario {$usuario->name} ha iniciado un nuevo servicio de movilidad.",
                'servicio_completado' => "Se completó una reserva asociada al vehículo {$vehiculo->placa}.",
                'servicio_cancelado' => "Un usuario canceló su reserva antes del inicio del servicio.",
                'conductor_disponible' => "El conductor {$usuario->name} está disponible para nuevas asignaciones.",
                'conductor_no_disponible' => "El conductor {$usuario->name} se ha desconectado temporalmente.",
                'vehiculo_asignado' => "El vehículo {$vehiculo->placa} fue asignado exitosamente a un conductor.",
                'mantenimiento_programado' => "Se ha programado un mantenimiento preventivo para el vehículo {$vehiculo->placa}.",
                'mantenimiento_completado' => "El vehículo {$vehiculo->placa} ha completado su mantenimiento correctamente.",
                'pago_recibido' => "Se recibió un pago de {$usuario->name} por un servicio.",
                'usuario_registrado' => "Nuevo registro de usuario en la plataforma: {$usuario->email}.",
                'alerta_generada' => "El sistema detectó una alerta de operación o telemetría.",
                default => "Evento genérico del sistema registrado correctamente.",
            };

            $datos = [
                'origen' => fake()->randomElement(['sistema', 'usuario', 'API']),
                'prioridad' => fake()->randomElement(['baja', 'media', 'alta']),
                'ubicacion' => fake()->randomElement(['Bucaramanga', 'Floridablanca', 'Girón', 'Piedecuesta']),
                'vehiculo' => $vehiculo->placa,
                'usuario' => $usuario->email,
                'accion' => $tipo,
                'resultado' => fake()->randomElement(['éxito', 'fallo', 'pendiente']),
            ];

            $eventos[] = [
                'tipo' => $tipo,
                'user_id' => $usuario->id,
                'vehiculo_id' => $vehiculo->id,
                'reserva_id' => $reserva?->id,
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'datos' => $datos,
                'ip' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'created_at' => $fecha,
                'updated_at' => $fecha->addMinutes(rand(1, 20)),
            ];
        }

        // Insertar en bloque para eficiencia
        foreach ($eventos as $data) {
            Event::create($data);
        }

        $this->command->info("✅ {$totalEventos} eventos creados con tipos variados y datos realistas.");
    }
}
