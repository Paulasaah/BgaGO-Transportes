<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Desempeño de Conductores</title>
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
        .rating {
            color: #EAB308;
            font-weight: bold;
        }
        .top-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .top-card {
            display: table-cell;
            background: #f8f9fa;
            border: 2px solid #4F46E5;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            width: 33%;
        }
        .top-card:not(:last-child) {
            margin-right: 10px;
        }
        .top-badge {
            background: #4F46E5;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
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
        <h1>Reporte de Desempeño de Conductores</h1>
        <p>Período: {{ ucfirst($periodo) }} | Generado: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Conductores</div>
                <div class="summary-value">{{ count($conductores) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Servicios Completados</div>
                <div class="summary-value">{{ array_sum(array_column($conductores, 'servicios_completados')) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Horas Trabajadas</div>
                <div class="summary-value">{{ array_sum(array_column($conductores, 'horas_trabajo')) }}h</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Calificación Promedio</div>
                <div class="summary-value rating">
                    {{ count($conductores) > 0 ? number_format(array_sum(array_column($conductores, 'calificacion_promedio')) / count($conductores), 1) : 0 }} ★
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Top 3 Conductores</div>
        <div class="top-section">
            @foreach(collect($conductores)->sortByDesc('servicios_completados')->take(3) as $index => $conductor)
            <div class="top-card">
                <div class="top-badge">#{{ $index + 1 }}</div>
                <div style="font-weight: bold; margin-bottom: 5px;">{{ $conductor['conductor'] }}</div>
                <div style="color: #666; font-size: 11px;">{{ $conductor['servicios_completados'] }} servicios</div>
                <div style="color: #10b981; font-weight: bold; margin-top: 5px;">
                    ${{ number_format($conductor['ingresos_generados']) }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="section">
        <div class="section-title">Detalle Completo por Conductor</div>
        <table>
            <thead>
                <tr>
                    <th>Conductor</th>
                    <th class="text-center">Servicios</th>
                    <th class="text-center">Calificación</th>
                    <th class="text-center">Horas</th>
                    <th class="text-right">Ingresos Generados</th>
                </tr>
            </thead>
            <tbody>
                @foreach($conductores as $conductor)
                <tr>
                    <td>{{ $conductor['conductor'] }}</td>
                    <td class="text-center">{{ $conductor['servicios_completados'] }}</td>
                    <td class="text-center rating">{{ $conductor['calificacion_promedio'] }} ★</td>
                    <td class="text-center">{{ $conductor['horas_trabajo'] }}h</td>
                    <td class="text-right">${{ number_format($conductor['ingresos_generados'], 2) }}</td>
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