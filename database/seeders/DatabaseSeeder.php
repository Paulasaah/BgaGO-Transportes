<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Ejecutar todos los seeders del sistema.
     */
    public function run(): void
    {
        $this->call([
            BranchSeeder::class,       // 🏢 Sedes (Centro, Cañaveral, etc.)
            UserSeeder::class,         // 👤 Usuarios base
            ReservationSeeder::class,  // 📋 Reservas de ejemplo
        ]);

        $this->command->info('✅ Todos los seeders ejecutados correctamente.');
    }
}
