<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // RES-2024-001
            
            // Relaciones
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('conductor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('sede_id')->constrained('branches')->cascadeOnDelete();
            
            // Datos de la reserva
            $table->enum('tipo', ['reserva', 'domicilio'])->default('reserva');
            $table->enum('estado', ['pendiente', 'confirmada', 'activa', 'completada', 'cancelada'])->default('pendiente');
            
            // Direcciones
            $table->text('origen_direccion');
            $table->decimal('origen_lat', 10, 7)->nullable();
            $table->decimal('origen_lng', 10, 7)->nullable();
            
            $table->text('destino_direccion');
            $table->decimal('destino_lat', 10, 7)->nullable();
            $table->decimal('destino_lng', 10, 7)->nullable();
            
            // Fechas y tiempos
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->dateTime('fecha_confirmacion')->nullable();
            $table->dateTime('fecha_inicio_real')->nullable();
            $table->dateTime('fecha_fin_real')->nullable();
            
            // Costos
            $table->decimal('monto', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('monto_final', 10, 2);
            
            // Detalles adicionales
            $table->text('notas_cliente')->nullable();
            $table->text('notas_conductor')->nullable();
            $table->text('notas_admin')->nullable();
            
            // Tracking
            $table->json('waypoints')->nullable(); // Puntos de ruta GPS
            $table->integer('distancia_km')->nullable();
            $table->integer('duracion_minutos')->nullable();
            
            // Calificación
            $table->tinyInteger('calificacion_conductor')->nullable(); // 1-5
            $table->text('comentario_conductor')->nullable();
            $table->tinyInteger('calificacion_cliente')->nullable(); // 1-5
            $table->text('comentario_cliente')->nullable();
            
            // Motivo de cancelación
            $table->text('motivo_cancelacion')->nullable();
            $table->foreignId('cancelado_por')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para optimizar consultas
            $table->index('codigo');
            $table->index('estado');
            $table->index('tipo');
            $table->index(['user_id', 'estado']);
            $table->index(['vehiculo_id', 'estado']);
            $table->index(['fecha_inicio', 'fecha_fin']);
            $table->index(['sede_id', 'estado']); // útil para filtrar reservas por sede

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};