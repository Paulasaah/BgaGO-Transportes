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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('direccion');
            $table->string('ciudad');
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();

            $table->integer('radio')->default(500)->comment('Radio de cobertura en metros');
            $table->string('color', 7)->default('#3b82f6');
            $table->text('descripcion')->nullable();
            $table->integer('capacidad_vehiculos')->default(10);
            $table->boolean('activa')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
