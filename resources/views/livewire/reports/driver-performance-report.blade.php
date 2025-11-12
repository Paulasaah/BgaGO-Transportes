<div class="space-y-6">
    {{-- Resumen General --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Total Conductores</div>
            <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ count($conductores) }}
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Servicios Completados</div>
            <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ array_sum(array_column($conductores, 'servicios_completados')) }}
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Horas Trabajadas</div>
            <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ array_sum(array_column($conductores, 'horas_trabajo')) }}h
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Calificación Promedio</div>
            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 flex items-center gap-1">
                <flux:icon.star class="size-6" />
                {{ count($conductores) > 0 ? number_format(array_sum(array_column($conductores, 'calificacion_promedio')) / count($conductores), 1) : 0 }}
            </div>
        </div>
    </div>

    {{-- Gráfico de Radar - Comparación de Conductores --}}
    @if(count($conductores) > 0)
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="mb-6">
                <flux:heading size="lg">Comparación de Desempeño</flux:heading>
                <flux:subheading>Calificación vs Servicios Completados</flux:subheading>
            </div>

            <div class="relative h-80">
                <canvas id="driverPerformanceChart"></canvas>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="text-center py-8">
                <flux:icon.user class="size-12 mx-auto text-zinc-400 mb-4" />
                <p class="text-zinc-600 dark:text-zinc-400">No hay datos de conductores para mostrar</p>
            </div>
        </div>
    @endif

    {{-- Ranking de Conductores --}}
    @if(count($conductores) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Top por Servicios --}}
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-800">
                    <div class="flex items-center gap-2">
                        <flux:icon.cube class="size-5 text-blue-600 dark:text-blue-400" />
                        <flux:heading size="md">Top Servicios</flux:heading>
                    </div>
                </div>
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach(collect($conductores)->sortByDesc('servicios_completados')->take(5) as $index => $conductor)
                        <div class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <div class="flex items-center gap-3">
                                <div class="size-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-sm">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white truncate">
                                        {{ $conductor['conductor'] }}
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $conductor['servicios_completados'] }} servicios
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Top por Calificación --}}
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border-b border-yellow-200 dark:border-yellow-800">
                    <div class="flex items-center gap-2">
                        <flux:icon.star class="size-5 text-yellow-600 dark:text-yellow-400" />
                        <flux:heading size="md">Top Calificación</flux:heading>
                    </div>
                </div>
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach(collect($conductores)->sortByDesc('calificacion_promedio')->take(5) as $index => $conductor)
                        <div class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <div class="flex items-center gap-3">
                                <div class="size-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center text-yellow-600 dark:text-yellow-400 font-bold text-sm">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white truncate">
                                        {{ $conductor['conductor'] }}
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400 flex items-center gap-1">
                                        <flux:icon.star class="size-3 text-yellow-500" />
                                        {{ $conductor['calificacion_promedio'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Top por Ingresos --}}
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div class="p-4 bg-green-50 dark:bg-green-900/20 border-b border-green-200 dark:border-green-800">
                    <div class="flex items-center gap-2">
                        <flux:icon.currency-dollar class="size-5 text-green-600 dark:text-green-400" />
                        <flux:heading size="md">Top Ingresos</flux:heading>
                    </div>
                </div>
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach(collect($conductores)->sortByDesc('ingresos_generados')->take(5) as $index => $conductor)
                        <div class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <div class="flex items-center gap-3">
                                <div class="size-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center text-green-600 dark:text-green-400 font-bold text-sm">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white truncate">
                                        {{ $conductor['conductor'] }}
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        ${{ number_format($conductor['ingresos_generados']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tabla Detallada --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                <flux:heading size="lg">Detalle Completo por Conductor</flux:heading>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Conductor
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Servicios
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Calificación
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Horas
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Ingresos Generados
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach($conductores as $conductor)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="px-6 py-4 text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $conductor['conductor'] }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <flux:badge variant="info">
                                        {{ $conductor['servicios_completados'] }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <flux:icon.star class="size-4 text-yellow-500" />
                                        <span class="font-medium text-zinc-900 dark:text-white">
                                            {{ $conductor['calificacion_promedio'] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-center text-zinc-600 dark:text-zinc-400">
                                    {{ $conductor['horas_trabajo'] }}h
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-zinc-900 dark:text-white">
                                    ${{ number_format($conductor['ingresos_generados']) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('livewire:initialized', () => {
    const ctx = document.getElementById('driverPerformanceChart');
    if (ctx) {
        // Datos pasados desde Livewire como propiedad pública
        const chartData = @js($this->chartData);
        
        // Verificar que hay datos antes de crear el gráfico
        if (chartData && chartData.labels && chartData.labels.length > 0) {
            const chart = new Chart(ctx, {
                type: 'radar',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    scales: {
                        r: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 20
                            }
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush