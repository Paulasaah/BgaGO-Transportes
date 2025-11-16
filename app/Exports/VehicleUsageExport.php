<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class VehicleUsageExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $vehiculos;
    protected $periodo;

    public function __construct($vehiculos, $periodo)
    {
        $this->vehiculos = $vehiculos;
        $this->periodo = $periodo;
    }

    public function collection()
    {
        return collect($this->vehiculos)->map(function ($vehiculo) {
            return [
                'vehiculo' => $vehiculo['vehiculo'],
                'servicios' => $vehiculo['servicios'],
                'horas_uso' => $vehiculo['horas_uso'] . 'h',
                'tasa_ocupacion' => $vehiculo['tasa_ocupacion'] . '%',
                'ingresos' => '$' . number_format($vehiculo['ingresos'], 2),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Vehículo',
            'Servicios',
            'Horas de Uso',
            'Tasa Ocupación',
            'Ingresos',
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
        return 'Uso de Vehículos';
    }
}