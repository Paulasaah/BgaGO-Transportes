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
            $table->string('direccion')->nullable();
            $table->decimal('lat', 10, 7);
            $table->decimal('lon', 10, 7);
            $table->integer('radio')->default(500)->comment('Radio en metros');
            $table->string('color', 7)->default('#3b82f6');
            $table->text('descripcion')->nullable();
            $table->integer('capacidad_vehiculos')->default(10);
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
