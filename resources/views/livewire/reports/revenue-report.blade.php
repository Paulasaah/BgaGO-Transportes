<div class="space-y-6">
    {{-- Resumen de Ingresos --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Total de Ingresos --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center gap-3 mb-4">
                <div class="size-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <flux:icon.currency-dollar class="size-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">Total de Ingresos</div>
                    <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                        ${{ number_format($ingresos['total'] ?? 0) }}
                    </div>
                </div>
            </div>
            
            @if(isset($ingresos['cambio_porcentual']) && $ingresos['cambio_porcentual'] > 0)
                <div class="flex items-center gap-2 text-sm">
                    <flux:badge variant="success" size="sm">
                        <flux:icon.arrow-trending-up class="size-3" />
                        +{{ $ingresos['cambio_porcentual'] }}%
                    </flux:badge>
                    <span class="text-zinc-600 dark:text-zinc-400">vs período anterior</span>
                </div>
            @endif
        </div>

        {{-- Distribución por Tipo --}}
        @if(isset($ingresos['por_tipo']) && count($ingresos['por_tipo']) > 0)
            @foreach($ingresos['por_tipo'] as $tipo)
                <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $tipo['tipo'] }}</div>
                            <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                                ${{ number_format($tipo['monto']) }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $tipo['porcentaje'] }}%
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">del total</div>
                        </div>
                    </div>
                    
                    {{-- Barra de progreso --}}
                    <div class="h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                        <div 
                            class="h-full bg-blue-500 transition-all duration-500" 
                            style="width: {{ $tipo['porcentaje'] }}%"
                        ></div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Gráfico de Ingresos por Día --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
        <div class="mb-6">
            <flux:heading size="lg">Evolución de Ingresos</flux:heading>
            <flux:subheading>Ingresos diarios durante el período seleccionado</flux:subheading>
        </div>

        <div class="relative h-80">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Tabla Detallada --}}
    @if(isset($ingresos['por_dia']) && count($ingresos['por_dia']) > 0)
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                <flux:heading size="lg">Detalle Diario</flux:heading>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Monto
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                % del Total
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach($ingresos['por_dia'] as $dia)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($dia['fecha'])->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-zinc-900 dark:text-white">
                                    ${{ number_format($dia['monto']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-zinc-600 dark:text-zinc-400">
                                    {{ $ingresos['total'] > 0 ? round(($dia['monto'] / $ingresos['total']) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-zinc-50 dark:bg-zinc-900">
                        <tr class="font-bold">
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-white">
                                TOTAL
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-zinc-900 dark:text-white">
                                ${{ number_format($ingresos['total'] ?? 0) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-zinc-900 dark:text-white">
                                100%
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('livewire:initialized', () => {
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        const chart = new Chart(ctx, {
            type: 'line',
            data: @json($chartData),
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush