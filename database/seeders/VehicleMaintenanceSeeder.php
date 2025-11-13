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

        if ($vehicles->isEmpty() || $users->isEmpty()) {
            $this->command->warn('⚠️ No hay vehículos o usuarios disponibles para mantenimiento.');
            return;
        }

        for ($i = 1; $i <= 10; $i++) {
            $vehicle = $vehicles->random();
            $fecha = now()->subDays($i);

            VehicleMaintenance::updateOrCreate(
                [
                    'vehiculo_id' => $vehicle->id,
                    'fecha_realizada' => $fecha->format('Y-m-d'),
                ],
                [
                    'realizado_por' => $users->random()->id,
                    'tipo' => 'preventivo',
                    'estado' => 'completado',
                    'kilometraje_actual' => 5000 + ($i * 300),
                    'kilometraje_proximo' => 8000 + ($i * 300),
                    'kilometraje_intervalo' => 3000,
                    'fecha_programada' => $fecha->subDays(2),
                    'costo' => 120000 + ($i * 5000),
                    'descripcion' => 'Cambio de aceite y revisión general.',
                    'repuestos_usados' => 'Aceite, filtro de aire, bujías.',
                    'taller' => 'MotoCenter Bucaramanga',
                    'mecanico' => 'Pedro López',
                    'observaciones' => 'Mantenimiento exitoso sin incidentes.',
                    'archivos' => ['factura.pdf', 'checklist.jpg'],
                ]
            );
        }

        $this->command->info('✅ Mantenimientos creados o actualizados correctamente.');
    }
}
