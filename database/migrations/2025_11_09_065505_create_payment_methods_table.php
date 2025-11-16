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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['efectivo', 'tarjeta', 'transferencia', 'mercadopago'])->default('efectivo');
            $table->boolean('activo')->default(true);
            $table->text('descripcion')->nullable();
            $table->json('config')->nullable()->comment('Configuración adicional, ej: claves API');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
