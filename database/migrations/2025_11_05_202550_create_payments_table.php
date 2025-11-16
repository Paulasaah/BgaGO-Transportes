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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_transaccion')->unique();
            
            // Relaciones
            $table->foreignId('reserva_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Detalles del pago
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'mercadopago']);
            $table->decimal('monto', 10, 2);
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'reembolsado'])->default('pendiente');
            
            // Info de la transacción
            $table->string('referencia_externa')->nullable(); // ID de pasarela de pago
            $table->json('datos_transaccion')->nullable(); // JSON con info adicional
            $table->text('motivo_rechazo')->nullable();
            $table->dateTime('fecha_aprobacion')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('estado');
            $table->index('metodo_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
