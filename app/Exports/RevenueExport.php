<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class RevenueExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $reporte;
    protected $periodo;

    public function __construct($reporte, $periodo)
    {
        $this->reporte = $reporte;
        $this->periodo = $periodo;
    }

    public function collection()
    {
        $data = collect();

        // Agregar resumen
        $data->push([
            'Resumen General',
            '',
            '',
        ]);
        $data->push([
            'Total de Ingresos',
            '$' . number_format($this->reporte['total'] ?? 0, 2),
            '',
        ]);
        
        if (isset($this->reporte['cambio_porcentual'])) {
            $data->push([
                'Cambio vs Período Anterior',
                $this->reporte['cambio_porcentual'] . '%',
                '',
            ]);
        }

        $data->push(['', '', '']);

        // Distribución por tipo
        if (isset($this->reporte['por_tipo']) && count($this->reporte['por_tipo']) > 0) {
            $data->push([
                'Distribución por Tipo',
                '',
                '',
            ]);
            foreach ($this->reporte['por_tipo'] as $tipo) {
                $data->push([
                    $tipo['tipo'],
                    '$' . number_format($tipo['monto'], 2),
                    $tipo['porcentaje'] . '%',
                ]);
            }
            $data->push(['', '', '']);
        }

        // Desglose diario
        if (isset($this->reporte['por_dia']) && count($this->reporte['por_dia']) > 0) {
            $data->push([
                'Desglose Diario',
                '',
                '',
            ]);
            foreach ($this->reporte['por_dia'] as $dia) {
                $porcentaje = $this->reporte['total'] > 0 
                    ? round(($dia['monto'] / $this->reporte['total']) * 100, 1) 
                    : 0;
                
                $data->push([
                    \Carbon\Carbon::parse($dia['fecha'])->format('d/m/Y'),
                    '$' . number_format($dia['monto'], 2),
                    $porcentaje . '%',
                ]);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Monto',
            '% del Total',
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
        return 'Reporte de Ingresos';
    }
}