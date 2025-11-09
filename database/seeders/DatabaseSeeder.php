<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BranchSeeder::class,
            DeliverySeeder::class,
            PaymentMethodSeeder::class,
            TransactionLogSeeder::class,
            ConfiguracionSeeder::class,
            VehicleSeeder::class,
            DriverProfileSeeder::class,
            ReservationSeeder::class,
            PaymentSeeder::class,
            VehicleMaintenanceSeeder::class,
            EventSeeder::class,
        ]);
    }
}
