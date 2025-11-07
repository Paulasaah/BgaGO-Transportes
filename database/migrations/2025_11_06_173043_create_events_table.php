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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            
            // Tipo de evento
            $table->enum('tipo', [
                'servicio_iniciado',
                'servicio_completado',
                'servicio_cancelado',
                'conductor_disponible',
                'conductor_no_disponible',
                'vehiculo_asignado',
                'mantenimiento_programado',
                'mantenimiento_completado',
                'pago_recibido',
                'usuario_registrado',
                'alerta_generada',
                'otro'
            ]);
            
            // Relaciones opcionales
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('reserva_id')->nullable()->constrained('reservations')->nullOnDelete();
            
            // Contenido
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->json('datos')->nullable(); // Datos adicionales en JSON
            
            // Metadata
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();
            
            $table->index('tipo');
            $table->index('created_at');
            $table->index(['user_id', 'tipo']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
