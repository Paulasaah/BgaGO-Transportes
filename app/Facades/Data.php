<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade para acceder al servicio de datos (Mock o Real)
 * 
 * @method static array getDashboardStats()
 * @method static array getReservasPorMes()
 * @method static array getDistribucionSedes()
 * @method static array getVehicleLocations()
 * @method static array getActiveRoutes()
 * @method static array getMapFilters()
 * @method static array getLiveServices()
 * @method static array getVehicleStatusSummary()
 * @method static array getAlerts()
 * @method static array getEventTimeline()
 * @method static array getCatalogVehicles(array $filters = [])
 * @method static array|null getVehicleById(int $id)
 * @method static array getVehicleAvailability(int $vehicleId, string $fecha)
 * @method static array getUserReservations(int $userId)
 * @method static array getRevenueReport(string $periodo = 'mes')
 * @method static array getVehicleUsageReport()
 * @method static array getDriverPerformance()
 * @method static array getUserStats()
 * @method static array getUsers(array $filters = [])
 * @method static array getDriverStats()
 * @method static array getDrivers(array $filters = [])
 * @method static array getVehicleStats()
 * @method static array getVehicles(array $filters = [])
 * @method static array getReservationStats()
 * @method static array getReservations(array $filters = [])
 * @method static string getStatusLabel(string $status)
 * @method static string getStatusColor(string $status)
 * @method static string getSeverityColor(string $severity)
 */
class Data extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'data.service';
    }
}