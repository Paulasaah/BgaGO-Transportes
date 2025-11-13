<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Facades\Data;

class ReportsIndex extends Component
{
    public $periodo = 'mes'; // mes, trimestre, año
    public $reporteSeleccionado = 'ingresos'; // ingresos, vehiculos, conductores

    // Datos de Ingresos
    public $ingresos = [];
    public $ingresosChart = [];

    // Datos de Vehículos
    public $vehiculosUsage = [];
    
    // Datos de Conductores
    public $conductoresPerformance = [];

    // Stats generales
    public $stats = [
        'total_servicios' => 0,
        'total_ingresos' => 0,
        'promedio_calificacion' => 0,
        'tasa_cancelacion' => 0
    ];

    public function mount()
    {
        $this->loadReportData();
    }

    public function updatedPeriodo()
    {
        $this->loadReportData();
    }

    public function updatedReporteSeleccionado()
    {
        $this->loadReportData();
    }

    public function loadReportData()
    {
        try {
            // Cargar datos según el tipo de reporte
            switch ($this->reporteSeleccionado) {
                case 'ingresos':
                    $this->loadIngresosReport();
                    break;
                case 'vehiculos':
                    $this->loadVehiculosReport();
                    break;
                case 'conductores':
                    $this->loadConductoresReport();
                    break;
            }

            // Cargar stats generales
            $this->loadGeneralStats();
        } catch (\Exception $e) {
            \Log::error('Error loading report data: ' . $e->getMessage());
        }
    }

    private function loadIngresosReport()
    {
        $data = Data::getRevenueReport($this->periodo);
        
        $this->ingresos = $data;
        
        // Preparar datos para gráfico
        $this->ingresosChart = [
            'labels' => array_column($data['por_dia'] ?? [], 'fecha'),
            'data' => array_column($data['por_dia'] ?? [], 'monto'),
            'por_tipo' => $data['por_tipo'] ?? []
        ];
    }

    private function loadVehiculosReport()
    {
        $this->vehiculosUsage = Data::getVehicleUsageReport();
    }

    private function loadConductoresReport()
    {
        $this->conductoresPerformance = Data::getDriverPerformance();
    }

    private function loadGeneralStats()
    {
        $reservationStats = Data::getReservationStats();
        
        $this->stats = [
            'total_servicios' => $reservationStats['activas'] + $reservationStats['completadas_hoy'],
            'total_ingresos' => $this->ingresos['total'] ?? 0,
            'promedio_calificacion' => 4.7,
            'tasa_cancelacion' => $reservationStats['canceladas_mes'] > 0 
                ? round(($reservationStats['canceladas_mes'] / ($reservationStats['activas'] + $reservationStats['completadas_hoy'])) * 100, 1)
                : 0
        ];
    }

    public function exportReport($format = 'pdf')
    {
        // Preparar para exportación futura
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => "Exportación a {$format} estará disponible próximamente"
        ]);
    }

    public function render()
    {
        return view('livewire.reports.reports-index');
    }
}