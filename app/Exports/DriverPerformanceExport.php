<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DriverPerformanceExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $conductores;
    protected $periodo;

    public function __construct($conductores, $periodo)
    {
        $this->conductores = $conductores;
        $this->periodo = $periodo;
    }

    public function collection()
    {
        return collect($this->conductores)->map(function ($conductor) {
            return [
                'conductor' => $conductor['conductor'],
                'servicios' => $conductor['servicios_completados'],
                'calificacion' => $conductor['calificacion_promedio'],
                'horas' => $conductor['horas_trabajo'] . 'h',
                'ingresos' => '$' . number_format($conductor['ingresos_generados'], 2),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Conductor',
            'Servicios Completados',
            'Calificación Promedio',
            'Horas Trabajadas',
            'Ingresos Generados',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Desempeño Conductores';
    }
}