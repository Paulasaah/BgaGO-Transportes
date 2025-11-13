<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleMaintenance;
use App\Models\Vehicle;
use App\Models\User;
use Carbon\Carbon;

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

        // Valores válidos según la migración
        $tipos = [
            'preventivo',
            'correctivo',
            'revision_general',
            'cambio_aceite',
            'llantas',
            'frenos',
            'bateria',
            'cadena',
            'ajuste_general',
            'otro'
        ];

        $estados = ['programado', 'en_proceso', 'completado', 'cancelado'];
        $talleres = [
            'MotoCenter Bucaramanga',
            'Taller Express Floridablanca',
            'EcoBikes Repuestos',
            'ServiMotos Girón',
            'Mecánica Total Real de Minas',
        ];
        $mecanicos = ['Pedro López', 'Andrés Díaz', 'Carlos Gómez', 'Javier Pinto', 'Luis Ramírez'];

        $mantenimientos = [];

        // 🔹 Generar entre 40 y 60 mantenimientos realistas
        $total = rand(40, 60);
        for ($i = 1; $i <= $total; $i++) {
            $vehiculo = $vehicles->random();
            $usuario = $users->random();
            $tipo = $tipos[array_rand($tipos)];
            $estado = $estados[array_rand($estados)];
            $taller = $talleres[array_rand($talleres)];
            $mecanico = $mecanicos[array_rand($mecanicos)];

            $fechaProgramada = Carbon::now()->subDays(rand(0, 120))->addDays(rand(0, 15));
            $fechaRealizada = in_array($estado, ['en_proceso', 'completado'])
                ? (clone $fechaProgramada)->addDays(rand(0, 10))
                : null;

            $kilometraje = rand(3000, 20000);

            // Costo y repuestos coherentes con el tipo
            $costo = match ($tipo) {
                'preventivo' => rand(100000, 250000),
                'correctivo' => rand(250000, 600000),
                'cambio_aceite' => rand(80000, 150000),
                'llantas' => rand(180000, 300000),
                'frenos' => rand(150000, 250000),
                'bateria' => rand(200000, 350000),
                'cadena' => rand(120000, 200000),
                'ajuste_general' => rand(100000, 220000),
                'revision_general' => rand(90000, 180000),
                default => rand(80000, 150000),
            };

            $repuestos = match ($tipo) {
                'preventivo' => 'Aceite, filtro de aire, bujías',
                'correctivo' => 'Pastillas de freno, batería, llanta delantera',
                'cambio_aceite' => 'Aceite de motor, filtro de aceite',
                'llantas' => 'Llantas delanteras y traseras',
                'frenos' => 'Pastillas, discos, líquido de frenos',
                'bateria' => 'Batería de gel, terminales nuevos',
                'cadena' => 'Cadena, piñón, sprocket',
                'ajuste_general' => 'Revisión general, lubricación, limpieza',
                'revision_general' => 'Chequeo eléctrico y mecánico',
                default => 'Revisión rápida de componentes básicos',
            };

            $descripcion = match ($tipo) {
                'preventivo' => 'Cambio de aceite y revisión general.',
                'correctivo' => 'Reparación de fallas detectadas y reemplazo de piezas.',
                'cambio_aceite' => 'Cambio de aceite completo y limpieza de filtro.',
                'llantas' => 'Reemplazo y balanceo de llantas.',
                'frenos' => 'Ajuste y cambio de pastillas de freno.',
                'bateria' => 'Cambio de batería y chequeo del sistema eléctrico.',
                'cadena' => 'Reemplazo y ajuste de cadena de transmisión.',
                'ajuste_general' => 'Ajuste general del sistema mecánico.',
                'revision_general' => 'Chequeo completo de frenos, luces y fluidos.',
                default => 'Mantenimiento general y verificación básica.',
            };

            $mantenimientos[] = [
                'vehiculo_id' => $vehiculo->id,
                'realizado_por' => $usuario->id,
                'tipo' => $tipo,
                'estado' => $estado,
                'kilometraje_actual' => $kilometraje,
                'kilometraje_proximo' => $kilometraje + rand(2000, 5000),
                'kilometraje_intervalo' => rand(2000, 5000),
                'fecha_programada' => $fechaProgramada,
                'fecha_realizada' => $fechaRealizada,
                'costo' => $costo,
                'descripcion' => $descripcion,
                'repuestos_usados' => $repuestos,
                'taller' => $taller,
                'mecanico' => $mecanico,
                'observaciones' => fake()->sentence(),
                'archivos' => ['factura_' . $i . '.pdf', 'checklist_' . $i . '.jpg'],
                'created_at' => $fechaProgramada,
                'updated_at' => $fechaRealizada ?? now(),
            ];
        }

        foreach ($mantenimientos as $data) {
            VehicleMaintenance::updateOrCreate(
                [
                    'vehiculo_id' => $data['vehiculo_id'],
                    'fecha_programada' => $data['fecha_programada'],
                ],
                $data
            );
        }

        $this->command->info("✅ {$total} mantenimientos creados correctamente con tipos y estados válidos.");
    }
}
