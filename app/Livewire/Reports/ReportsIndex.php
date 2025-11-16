<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Facades\Data;
use App\Exports\RevenueExport;
use App\Exports\VehicleUsageExport;
use App\Exports\DriverPerformanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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

    // ========== MÉTODOS DE EXPORTACIÓN ==========
    
    public function exportReport($format)
    {
        try {
            $timestamp = now()->format('Y-m-d_His');
            
            switch ($this->reporteSeleccionado) {
                case 'ingresos':
                    return $this->exportIngresos($format, $timestamp);
                    
                case 'vehiculos':
                    return $this->exportVehiculos($format, $timestamp);
                    
                case 'conductores':
                    return $this->exportConductores($format, $timestamp);
                    
                default:
                    $this->dispatch('notify', [
                        'type' => 'error',
                        'message' => 'Tipo de reporte no válido'
                    ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error al exportar reporte: ' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al exportar: ' . $e->getMessage()
            ]);
        }
    }

    private function exportIngresos($format, $timestamp)
    {
        $reporte = Data::getRevenueReport($this->periodo);
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('pdf.revenue-report', [
                'reporte' => $reporte,
                'periodo' => $this->periodo
            ]);
            
            return response()->streamDownload(
                fn() => print($pdf->output()),
                "reporte_ingresos_{$timestamp}.pdf"
            );
        }
        
        if ($format === 'excel') {
            return Excel::download(
                new RevenueExport($reporte, $this->periodo),
                "reporte_ingresos_{$timestamp}.xlsx"
            );
        }
        
        if ($format === 'csv') {
            return Excel::download(
                new RevenueExport($reporte, $this->periodo),
                "reporte_ingresos_{$timestamp}.csv"
            );
        }
    }

    private function exportVehiculos($format, $timestamp)
    {
        $vehiculos = Data::getVehicleUsageReport();
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('pdf.vehicle-usage-report', [
                'vehiculos' => $vehiculos,
                'periodo' => $this->periodo
            ]);
            
            return response()->streamDownload(
                fn() => print($pdf->output()),
                "reporte_vehiculos_{$timestamp}.pdf"
            );
        }
        
        if ($format === 'excel') {
            return Excel::download(
                new VehicleUsageExport($vehiculos, $this->periodo),
                "reporte_vehiculos_{$timestamp}.xlsx"
            );
        }
        
        if ($format === 'csv') {
            return Excel::download(
                new VehicleUsageExport($vehiculos, $this->periodo),
                "reporte_vehiculos_{$timestamp}.csv"
            );
        }
    }

    private function exportConductores($format, $timestamp)
    {
        $conductores = Data::getDriverPerformance();
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('pdf.driver-performance-report', [
                'conductores' => $conductores,
                'periodo' => $this->periodo
            ]);
            
            return response()->streamDownload(
                fn() => print($pdf->output()),
                "reporte_conductores_{$timestamp}.pdf"
            );
        }
        
        if ($format === 'excel') {
            return Excel::download(
                new DriverPerformanceExport($conductores, $this->periodo),
                "reporte_conductores_{$timestamp}.xlsx"
            );
        }
        
        if ($format === 'csv') {
            return Excel::download(
                new DriverPerformanceExport($conductores, $this->periodo),
                "reporte_conductores_{$timestamp}.csv"
            );
        }
    }

    public function render()
    {
        return view('livewire.reports.reports-index');
    }
}