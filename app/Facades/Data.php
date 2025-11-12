<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade para DataService / MockDataService
 * 
 * Permite acceder al servicio correcto según el modo configurado
 * en config/app.php (use_mock_data = true/false)
 *
 * @method static array getDashboardStats()
 * @method static array getReservasPorMes()
 * @method static array getDistribucionSedes()
 * @method static array getLiveServices()
 * @method static array getVehicleStatusSummary()
 * @method static array getAlerts()
 * @method static array getEventTimeline()
 * @method static array getVehicleLocations()
 * @method static array getRevenueReport(string $periodo = 'mes')
 * @method static array getVehicleUsageReport()
 * @method static array getDriverPerformance()
 * @method static array getReservationStats()
 * @method static string getStatusLabel(string $status)
 * @method static string getStatusColor(string $status)
 * @method static string getSeverityColor(string $severity)
 * 
 * @see \App\Services\DataService
 * @see \App\Services\MockDataService
 */
class Data extends Facade
{
    /**
     * Identificador del servicio registrado en el contenedor.
     * 
     * Debe coincidir con el binding en AppServiceProvider.
     */
    protected static function getFacadeAccessor()
    {
        return 'data.service';
    }
}
