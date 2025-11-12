<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Delivery;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Vehicle;
use Carbon\Carbon;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $reservations = Reservation::all();
        $vehicles = Vehicle::all();

        $clientes = $users->filter(fn($u) => $u->hasRole('cliente'));
        $conductores = $users->filter(fn($u) => $u->hasRole('conductor'));

        if ($clientes->isEmpty() || $conductores->isEmpty() || $vehicles->isEmpty()) {
            $this->command->warn('⚠️ No hay usuarios, conductores o vehículos suficientes para crear entregas.');
            return;
        }

        $estados = ['pendiente', 'confirmado', 'asignado', 'en_camino', 'entregado', 'cancelado'];
        $tipos = ['paquete', 'vehiculo'];
        $zonas = [
            ['origen' => 'Sucursal Cabecera', 'destino' => 'Calle 45 #28-90, Bucaramanga'],
            ['origen' => 'Sucursal Cañaveral', 'destino' => 'Calle 105 #30-45, Floridablanca'],
            ['origen' => 'Sucursal Real de Minas', 'destino' => 'Carrera 29 #45-32, Bucaramanga'],
            ['origen' => 'Sucursal Girón', 'destino' => 'Cra 27 #18-60, Girón'],
            ['origen' => 'Sucursal Piedecuesta', 'destino' => 'Carrera 8 #5-20, Piedecuesta'],
        ];

        $deliveries = [];

        // 🔹 Generar 80 entregas distribuidas temporalmente
        for ($i = 1; $i <= 80; $i++) {
            $cliente = $clientes->random();
            $conductor = $conductores->random();
            $vehiculo = $vehicles->random();
            $zona = $zonas[array_rand($zonas)];
            $estado = $estados[array_rand($estados)];
            $tipo = $tipos[array_rand($tipos)];

            // Fechas realistas
            $fechaCreacion = Carbon::now()->subDays(rand(0, 90));
            $fechaEstimada = (clone $fechaCreacion)->addHours(rand(1, 5));
            $fechaReal = in_array($estado, ['entregado', 'cancelado'])
                ? (clone $fechaEstimada)->addMinutes(rand(15, 90))
                : null;

            $costo = rand(15000, 70000);

            $deliveries[] = [
                'tipo' => $tipo,
                'descripcion' => ucfirst($tipo) . ' programado desde ' . $zona['origen'],
                'direccion_origen' => $zona['origen'],
                'direccion_destino' => $zona['destino'],
                'estado' => $estado,
                'vehiculo_id' => $vehiculo->id,
                'user_id' => $cliente->id,
                'conductor_id' => $conductor->id,
                'lat_origen' => 7.1193 + mt_rand(-100, 100) / 1000,
                'lon_origen' => -73.1227 + mt_rand(-100, 100) / 1000,
                'lat_destino' => 7.0738 + mt_rand(-100, 100) / 1000,
                'lon_destino' => -73.1051 + mt_rand(-100, 100) / 1000,
                'costo' => $costo,
                'fecha_entrega_estimada' => $fechaEstimada,
                'fecha_entrega_real' => $fechaReal,
                'notas_entrega' => fake()->sentence(),
                'created_at' => $fechaCreacion,
                'updated_at' => $fechaReal ?? now(),
            ];
        }

        // 🔹 Crear o actualizar entregas
        foreach ($deliveries as $data) {
            $delivery = Delivery::updateOrCreate(
                [
                    'descripcion' => $data['descripcion'],
                    'direccion_destino' => $data['direccion_destino']
                ],
                $data
            );

            // Asociar con una reserva aleatoria existente
            if ($reservations->isNotEmpty() && rand(0, 1)) {
                $reserva = $reservations->random();
                $delivery->update(['reserva_id' => $reserva->id]);
            }
        }

        $this->command->info('✅ 80 entregas generadas con estados variados y vínculos a reservas.');
    }
}
