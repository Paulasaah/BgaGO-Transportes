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
        Schema::create('vehicle_maintenances', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('vehiculo_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('realizado_por')->nullable()->constrained('users')->nullOnDelete();

            // Tipo y estado del mantenimiento
            $table->enum('tipo', [
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
            ])->comment('Tipo de mantenimiento realizado');

            $table->enum('estado', [
                'programado', 
                'en_proceso', 
                'completado', 
                'cancelado'
            ])->default('programado')->comment('Estado actual del mantenimiento');

            // Control por kilometraje
            $table->integer('kilometraje_actual')->nullable()->comment('Kilometraje al momento del mantenimiento');
            $table->integer('kilometraje_proximo')->nullable()->comment('Kilometraje estimado para el próximo mantenimiento');
            $table->integer('kilometraje_intervalo')->nullable()->comment('Intervalo recomendado entre mantenimientos en km');

            // Fechas de mantenimiento (secundarias)
            $table->date('fecha_programada')->nullable();
            $table->date('fecha_realizada')->nullable();

            // Costos y detalles
            $table->decimal('costo', 10, 2)->default(0)->comment('Costo total del mantenimiento');
            $table->text('descripcion')->comment('Descripción general del mantenimiento realizado');
            $table->text('repuestos_usados')->nullable()->comment('Lista de repuestos utilizados');
            $table->string('taller')->nullable()->comment('Nombre del taller o proveedor');
            $table->string('mecanico')->nullable()->comment('Nombre del mecánico encargado');
            $table->text('observaciones')->nullable()->comment('Observaciones adicionales');

            // Archivos adjuntos (facturas, fotos, reportes)
            $table->json('archivos')->nullable()->comment('Archivos adjuntos del mantenimiento');

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('vehiculo_id');
            $table->index('estado');
            $table->index('tipo');
            $table->index('kilometraje_actual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_maintenances');
    }
};
