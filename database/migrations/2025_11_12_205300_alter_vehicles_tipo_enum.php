<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Asegurar que la columna 'tipo' incluya todas las opciones necesarias
        DB::statement("ALTER TABLE vehicles MODIFY tipo ENUM('bicicleta','scooter','patineta','moto') DEFAULT 'scooter'");
    }

    public function down(): void
    {
        // Revertir a un subconjunto seguro si fuese necesario
        DB::statement("ALTER TABLE vehicles MODIFY tipo ENUM('bicicleta','scooter','moto') DEFAULT 'moto'");
    }
};