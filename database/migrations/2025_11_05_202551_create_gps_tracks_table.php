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
        Schema::create('gps_tracks', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('vehiculo_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('reserva_id')->nullable()->constrained('reservations')->nullOnDelete();

            // Coordenadas geográficas
            $table->decimal('latitud', 10, 7)->comment('Latitud del vehículo');
            $table->decimal('longitud', 10, 7)->comment('Longitud del vehículo');
            $table->decimal('altitud', 8, 2)->nullable()->comment('Altitud en metros');
            $table->decimal('precision', 8, 2)->nullable()->comment('Precisión del GPS en metros');

            // Datos de movimiento
            $table->decimal('velocidad', 6, 2)->nullable()->comment('Velocidad en km/h');

            // Estado del vehículo
            $table->boolean('motor_encendido')->default(false)->comment('Estado del motor: encendido/apagado');
            $table->unsignedTinyInteger('nivel_bateria')->nullable()->comment('Porcentaje de batería (0-100)');
            $table->integer('kilometraje')->nullable()->comment('Kilometraje registrado en este punto');
            $table->decimal('temperatura_motor', 5, 2)->nullable()->comment('Temperatura del motor en °C');
            $table->enum('fuente', ['gps', 'app_conductor', 'manual'])->default('gps')->comment('Origen de la información');

            // Registro temporal
            $table->timestamp('fecha_registro')->useCurrent()->index()->comment('Fecha y hora del registro del GPS');

            $table->timestamps();

            // Índices optimizados
            $table->index('vehiculo_id');
            $table->index('reserva_id');
            $table->index(['vehiculo_id', 'fecha_registro']);
            $table->index(['latitud', 'longitud']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gps_tracks');
    }
};
