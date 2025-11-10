<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleMaintenance;
use App\Models\Vehicle;
use App\Models\User;

class VehicleMaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = Vehicle::all();
        $users = User::all();

        for ($i = 1; $i <= 10; $i++) {
            VehicleMaintenance::create([
                'vehiculo_id' => $vehicles->random()->id,
                'realizado_por' => $users->random()->id,
                'tipo' => 'preventivo',
                'estado' => 'completado',
                'kilometraje_actual' => 5000 + ($i * 300),
                'kilometraje_proximo' => 8000 + ($i * 300),
                'kilometraje_intervalo' => 3000,
                'fecha_programada' => now()->subDays($i * 2),
                'fecha_realizada' => now()->subDays($i),
                'costo' => 120000 + ($i * 5000),
                'descripcion' => 'Cambio de aceite y revisión general.',
                'repuestos_usados' => 'Aceite, filtro de aire, bujías.',
                'taller' => 'MotoCenter Bucaramanga',
                'mecanico' => 'Pedro López',
                'observaciones' => 'Mantenimiento exitoso sin incidentes.',
                'archivos' => ['factura.pdf', 'checklist.jpg'],
            ]);
        }
    }
}
