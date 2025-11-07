<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            // Información básica
            $table->string('placa')->unique();
            $table->string('marca');
            $table->string('modelo');
            $table->year('year')->nullable();
            $table->enum('tipo', ['bicicleta', 'scooter', 'moto'])->default('moto');
            $table->string('color')->nullable();

            // Relaciones
            $table->foreignId('sede_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('conductor_id')->nullable()->constrained('users')->nullOnDelete();

            // Estado y disponibilidad
            $table->enum('estado', ['disponible', 'ocupado', 'mantenimiento', 'inactivo'])->default('disponible');
            $table->boolean('visible_catalogo')->default(true);

            // Precios base
            $table->decimal('precio_hora', 10, 2)->default(0);
            $table->decimal('precio_dia', 10, 2)->default(0);

            // Multimedia y descripción
            $table->string('imagen_principal')->nullable();
            $table->text('descripcion')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['estado', 'sede_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
