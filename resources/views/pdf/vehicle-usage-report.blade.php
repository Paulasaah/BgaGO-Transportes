<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Uso de Vehículos</title>
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
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            display: inline-block;
        }
        .progress-fill {
            height: 100%;
            background: #10b981;
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
        <h1>Reporte de Uso de Vehículos</h1>
        <p>Período: {{ ucfirst($periodo) }} | Generado: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Vehículos</div>
                <div class="summary-value">{{ count($vehiculos) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Servicios</div>
                <div class="summary-value">{{ array_sum(array_column($vehiculos, 'servicios')) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Horas Totales</div>
                <div class="summary-value">{{ array_sum(array_column($vehiculos, 'horas_uso')) }}h</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Ingresos Totales</div>
                <div class="summary-value">${{ number_format(array_sum(array_column($vehiculos, 'ingresos')) / 1000, 0) }}K</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Detalle por Vehículo</div>
        <table>
            <thead>
                <tr>
                    <th>Vehículo</th>
                    <th class="text-center">Servicios</th>
                    <th class="text-center">Horas de Uso</th>
                    <th class="text-center">Tasa Ocupación</th>
                    <th class="text-right">Ingresos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehiculos as $vehiculo)
                <tr>
                    <td>{{ $vehiculo['vehiculo'] }}</td>
                    <td class="text-center">{{ $vehiculo['servicios'] }}</td>
                    <td class="text-center">{{ $vehiculo['horas_uso'] }}h</td>
                    <td class="text-center">{{ $vehiculo['tasa_ocupacion'] }}%</td>
                    <td class="text-right">${{ number_format($vehiculo['ingresos'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Top 3 Vehículos por Ingresos</div>
        @php
            $topVehiculos = collect($vehiculos)->sortByDesc('ingresos')->take(3);
        @endphp
        <table>
            <thead>
                <tr>
                    <th>Posición</th>
                    <th>Vehículo</th>
                    <th class="text-center">Servicios</th>
                    <th class="text-right">Ingresos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topVehiculos as $index => $vehiculo)
                <tr>
                    <td style="font-weight: bold; color: #4F46E5;">{{ $index + 1 }}</td>
                    <td>{{ $vehiculo['vehiculo'] }}</td>
                    <td class="text-center">{{ $vehiculo['servicios'] }}</td>
                    <td class="text-right">${{ number_format($vehiculo['ingresos'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>BgaGO - Sistema de Gestión de Transporte</p>
        <p>Documento generado automáticamente el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>