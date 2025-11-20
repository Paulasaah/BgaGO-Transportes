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
        Schema::table('telemetrias', function (Blueprint $table) {
            $table->float('route_progress')
                ->default(0)
                ->comment('0-1 progreso de ruta actual')
                ->after('trip_count');

            $table->float('distance_travelled_km')
                ->default(0)
                ->comment('Distancia recorrida en la ruta actual (km)')
                ->after('route_progress');

            $table->float('total_distance_km')
                ->default(0)
                ->comment('Distancia total planificada de la ruta actual (km)')
                ->after('distance_travelled_km');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telemetrias', function (Blueprint $table) {
            $table->dropColumn([
                'route_progress',
                'distance_travelled_km',
                'total_distance_km',
            ]);
        });
    }
};
