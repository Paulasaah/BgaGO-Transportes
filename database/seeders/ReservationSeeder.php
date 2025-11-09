<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Models\Branch;
use App\Models\User;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $vehicles = Vehicle::limit(3)->get();
        $users = User::limit(3)->get();

        // Reserva activa 1: Centro → Cañaveral
        Reservation::create([
            'user_id' => $users[0]->id,
            'vehicle_id' => $vehicles[0]->id ?? null,
            'service_type' => 'domicilio',
            'status' => 'active',
            'pickup_address' => 'Carrera 19 #35-10, Centro',
            'dropoff_address' => 'Calle 48 #29-20, Cañaveral',
            'starts_at' => now(),
            'ends_at' => now()->addMinutes(15),
            'distance_km' => 3.5,
            'price_cents' => 15000,
        ]);

        // Reserva activa 2: Cabecera → Floridablanca
        Reservation::create([
            'user_id' => $users[1]->id,
            'vehicle_id' => $vehicles[1]->id ?? null,
            'service_type' => 'domicilio',
            'status' => 'active',
            'pickup_address' => 'Carrera 36 #48-15, Cabecera',
            'dropoff_address' => 'Calle 7 #8-30, Floridablanca',
            'starts_at' => now(),
            'ends_at' => now()->addMinutes(20),
            'distance_km' => 5.8,
            'price_cents' => 22000,
        ]);

        // Reserva pendiente
        Reservation::create([
            'user_id' => $users[2]->id,
            'service_type' => 'rental',
            'status' => 'pending',
            'pickup_address' => 'Centro Comercial Cacique',
            'dropoff_address' => 'Parque San Pío',
            'starts_at' => now()->addHour(),
            'distance_km' => 2.1,
            'price_cents' => 10000,
        ]);

        $this->command->info('✅ Reservas de ejemplo creadas');
    }
}