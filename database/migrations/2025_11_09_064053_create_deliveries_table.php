<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehicles');
            $table->foreignId('reserva_id')->nullable()->constrained('reservations')->nullOnDelete();

            $table->string('tipo', 50);
            $table->text('descripcion')->nullable();

            $table->string('direccion_origen');
            $table->decimal('lat_origen', 10, 7)->nullable();
            $table->decimal('lon_origen', 10, 7)->nullable();

            $table->string('direccion_destino');
            $table->decimal('lat_destino', 10, 7)->nullable();
            $table->decimal('lon_destino', 10, 7)->nullable();

            $table->decimal('costo', 10, 2);
            $table->enum('estado', ['pendiente', 'en_camino', 'entregado', 'cancelado'])->default('pendiente');
            $table->dateTime('fecha_entrega_estimada')->nullable();
            $table->dateTime('fecha_entrega_real')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
