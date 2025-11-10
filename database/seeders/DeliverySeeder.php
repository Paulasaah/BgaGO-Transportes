<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Delivery;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Vehicle;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $reservations = Reservation::all();
        $vehicles = Vehicle::all();

        if ($users->isEmpty() || $vehicles->isEmpty()) {
            $this->command->warn('⚠️ No hay usuarios o vehículos disponibles para crear entregas.');
            return;
        }

        $data = [
            [
                'tipo' => 'paquete',
                'descripcion' => 'Entrega de casco adicional.',
                'direccion_origen' => 'Sucursal Cabecera',
                'direccion_destino' => 'Calle 45 #28-90, Bucaramanga',
                'costo' => 8000,
                'estado' => 'entregado',
            ],
            [
                'tipo' => 'vehiculo',
                'descripcion' => 'Entrega de motocicleta al cliente.',
                'direccion_origen' => 'Sucursal Cañaveral',
                'direccion_destino' => 'Calle 105 #30-45, Floridablanca',
                'costo' => 12000,
                'estado' => 'en_camino',
            ],
            [
                'tipo' => 'paquete',
                'descripcion' => 'Entrega de documento olvidado.',
                'direccion_origen' => 'Sucursal Floridablanca',
                'direccion_destino' => 'Av. La Rosita #22-15, Bucaramanga',
                'costo' => 9000,
                'estado' => 'pendiente',
            ],
            [
                'tipo' => 'vehiculo',
                'descripcion' => 'Entrega del vehículo Honda Wave.',
                'direccion_origen' => 'Sucursal Piedecuesta',
                'direccion_destino' => 'Cra 27 #18-60, Girón',
                'costo' => 15000,
                'estado' => 'pendiente',
            ],
        ];

        foreach ($data as $d) {
            Delivery::updateOrCreate(
                ['descripcion' => $d['descripcion']], // clave única
                array_merge($d, [
                    'vehiculo_id' => $vehicles->random()->id, // ✅ Asociación con vehículo
                    'user_id' => $users->random()->id,
                    'reserva_id' => $reservations->isNotEmpty() ? $reservations->random()->id : null,
                    'lat_origen' => 7.1193,
                    'lon_origen' => -73.1227,
                    'lat_destino' => 7.0738,
                    'lon_destino' => -73.1051,
                    'fecha_entrega_estimada' => now()->addHours(1),
                    'fecha_entrega_real' => null,
                ])
            );
        }

        $this->command->info('✅ Entregas creadas o actualizadas correctamente con vehículos asociados.');
    }
}
