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

        $data = [
            [
                'tipo' => 'paquete',
                'descripcion' => 'Entrega de casco adicional.',
                'direccion_origen' => 'Sucursal Cabecera',
                'direccion_destino' => 'Calle 45 #28-90, Bucaramanga',
                'estado' => 'pendiente',
            ],
            [
                'tipo' => 'vehiculo',
                'descripcion' => 'Entrega de motocicleta al cliente.',
                'direccion_origen' => 'Sucursal Cañaveral',
                'direccion_destino' => 'Calle 105 #30-45, Floridablanca',
                'estado' => 'confirmado', // ✅ lista para iniciar
            ],
            [
                'tipo' => 'paquete',
                'descripcion' => 'Entrega de documento olvidado.',
                'direccion_origen' => 'Sucursal Floridablanca',
                'direccion_destino' => 'Av. La Rosita #22-15, Bucaramanga',
                'estado' => 'asignado',
            ],
            [
                'tipo' => 'vehiculo',
                'descripcion' => 'Entrega del vehículo Honda Wave.',
                'direccion_origen' => 'Sucursal Piedecuesta',
                'direccion_destino' => 'Cra 27 #18-60, Girón',
                'estado' => 'en_camino',
            ],
            [
                'tipo' => 'paquete',
                'descripcion' => 'Entrega de accesorios adicionales.',
                'direccion_origen' => 'Sucursal Real de Minas',
                'direccion_destino' => 'Carrera 29 #45-32, Bucaramanga',
                'estado' => 'entregado',
            ],
        ];

        foreach ($data as $d) {
            $cliente = $clientes->random();
            $conductor = $conductores->random();
            $vehiculo = $vehicles->random();

            $delivery = Delivery::updateOrCreate(
                ['descripcion' => $d['descripcion']],
                array_merge($d, [
                    'vehiculo_id' => $vehiculo->id,
                    'user_id' => $cliente->id,
                    'conductor_id' => $conductor->id,
                    'lat_origen' => 7.1193,
                    'lon_origen' => -73.1227,
                    'lat_destino' => 7.0738,
                    'lon_destino' => -73.1051,
                    'costo' => fake()->randomFloat(2, 15000, 70000),
                    'fecha_entrega_estimada' => now()->addHours(1),
                    'fecha_entrega_real' => null,
                    'notas_entrega' => fake()->sentence(),
                ])
            );

            if ($reservations->isNotEmpty()) {
                $reservation = $reservations->random();
                $reservation->update(['conductor_id' => $conductor->id]);
                $delivery->update(['reserva_id' => $reservation->id]);
            }
        }

        $this->command->info('✅ Entregas creadas con estados pendientes, confirmadas, asignadas, en curso y completadas.');
    }
}
