<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ingresos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #4F46E5;
        }
        .header h1 {
            color: #4F46E5;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            padding: 10px;
            text-align: center;
            border-right: 1px solid #ddd;
        }
        .summary-item:last-child {
            border-right: none;
        }
        .summary-label {
            color: #666;
            font-size: 11px;
            margin-bottom: 5px;
        }
        .summary-value {
            color: #1a1a1a;
            font-size: 20px;
            font-weight: bold;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            color: #4F46E5;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #4F46E5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background: #4F46E5;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background: #10b981;
            color: white;
        }
        .badge-danger {
            background: #ef4444;
            color: white;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Ingresos</h1>
        <p>Período: {{ ucfirst($periodo) }} | Generado: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total de Ingresos</div>
                <div class="summary-value">${{ number_format($reporte['total'] ?? 0, 2) }}</div>
            </div>
            @if(isset($reporte['cambio_porcentual']))
            <div class="summary-item">
                <div class="summary-label">Cambio vs Período Anterior</div>
                <div class="summary-value">
                    <span class="badge {{ $reporte['cambio_porcentual'] > 0 ? 'badge-success' : 'badge-danger' }}">
                        {{ $reporte['cambio_porcentual'] > 0 ? '+' : '' }}{{ $reporte['cambio_porcentual'] }}%
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if(isset($reporte['por_tipo']) && count($reporte['por_tipo']) > 0)
    <div class="section">
        <div class="section-title">Distribución por Tipo</div>
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th class="text-right">Monto</th>
                    <th class="text-center">Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reporte['por_tipo'] as $tipo)
                <tr>
                    <td>{{ $tipo['tipo'] }}</td>
                    <td class="text-right">${{ number_format($tipo['monto'], 2) }}</td>
                    <td class="text-center">{{ $tipo['porcentaje'] }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(isset($reporte['por_dia']) && count($reporte['por_dia']) > 0)
    <div class="section">
        <div class="section-title">Desglose Diario</div>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th class="text-right">Monto</th>
                    <th class="text-center">% del Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reporte['por_dia'] as $dia)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($dia['fecha'])->format('d/m/Y') }}</td>
                    <td class="text-right">${{ number_format($dia['monto'], 2) }}</td>
                    <td class="text-center">
                        {{ $reporte['total'] > 0 ? round(($dia['monto'] / $reporte['total']) * 100, 1) : 0 }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f8f9fa; font-weight: bold;">
                    <td>TOTAL</td>
                    <td class="text-right">${{ number_format($reporte['total'] ?? 0, 2) }}</td>
                    <td class="text-center">100%</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>BgaGO - Sistema de Gestión de Transporte</p>
        <p>Documento generado automáticamente el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>