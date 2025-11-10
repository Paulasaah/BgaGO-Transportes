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

            // Datos de remitente y destinatario
            $table->string('nombre_remitente')->nullable();
            $table->string('telefono_remitente')->nullable();
            $table->string('nombre_destinatario')->nullable();
            $table->string('telefono_destinatario')->nullable();

            // Detalles del contenido
            $table->text('descripcion_contenido')->nullable();
            $table->decimal('peso_estimado', 8, 2)->nullable();
            $table->boolean('es_fragil')->default(false);
            $table->text('instrucciones_especiales')->nullable();

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

            // Proceso y notas
            $table->dateTime('fecha_recogida')->nullable();
            $table->dateTime('fecha_entrega')->nullable();
            $table->text('notas_entrega')->nullable();

            $table->timestamps();

            $table->softDeletes(); // <--- agrega esta línea al final

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
