<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([

            // Seeders Base
            RolePermissionSeeder::class,
            UserSeeder::class,
            
            //Seeders funcionales
            BranchSeeder::class,
            PaymentMethodSeeder::class,
            TransactionLogSeeder::class,
            ConfiguracionSeeder::class,
            VehicleSeeder::class,
            DriverProfileSeeder::class,
            ReservationSeeder::class,
            DeliverySeeder::class,
            PaymentSeeder::class,
            VehicleMaintenanceSeeder::class,
            EventSeeder::class,

        ]);

        $this->command->info('✅ Todos los seeders ejecutados correctamente.');
    }
}
