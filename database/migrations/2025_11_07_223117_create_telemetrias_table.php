<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('telemetrias', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->index();
            
            // Información del dispositivo
            $table->enum('device_type', ['vehiculo', 'conductor'])->default('vehiculo');
            $table->enum('status', ['active', 'idle', 'charging', 'maintenance', 'offline'])->default('active');
            
            // Coordenadas
            $table->double('lat', 15, 8);
            $table->double('lon', 15, 8);
            $table->float('alt')->nullable();
            
            // Telemetría básica
            $table->float('battery')->default(100);
            $table->float('speed')->nullable()->comment('km/h');
            
            // Ubicación en red de sedes
            $table->string('current_branch')->nullable()->comment('Sede actual');
            $table->string('target_branch')->nullable()->comment('Destino');
            
            // Métricas de desgaste
            $table->float('odometer')->default(0)->comment('Kilómetros totales recorridos');
            $table->integer('trip_count')->default(0)->comment('Número de viajes realizados');
            $table->float('battery_health')->default(100)->comment('Salud de batería %');
            $table->timestamp('last_maintenance')->nullable();
            $table->float('maintenance_km_left')->default(1000)->comment('KM hasta próximo mantenimiento');
            
            // Para conductores (domiciliarios)
            $table->string('driver_name')->nullable();
            $table->integer('deliveries_completed')->default(0);
            $table->float('rating')->nullable()->comment('Calificación promedio 1-5');
            
            $table->timestamps();
            
            // Índices para mejor rendimiento y limpieza
            $table->index(['device_type', 'status']);
            $table->index('current_branch');
            $table->index('battery_health');
            $table->index(['device_id', 'created_at']); // Para limpieza y deduplicación
            $table->index('created_at'); // Para limpieza por fecha
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telemetrias');
    }
};