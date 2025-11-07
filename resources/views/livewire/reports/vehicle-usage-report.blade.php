<div class="space-y-6">
    {{-- Resumen General --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Total Vehículos</div>
            <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ count($vehiculos) }}
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Total Servicios</div>
            <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ array_sum(array_column($vehiculos, 'servicios')) }}
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Horas Totales</div>
            <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ array_sum(array_column($vehiculos, 'horas_uso')) }}h
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Ingresos Totales</div>
            <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                ${{ number_format(array_sum(array_column($vehiculos, 'ingresos')) / 1000, 0) }}K
            </div>
        </div>
    </div>

    {{-- Gráfico de Comparación --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
        <div class="mb-6">
            <flux:heading size="lg">Comparación de Uso por Vehículo</flux:heading>
            <flux:subheading>Servicios completados y horas de uso</flux:subheading>
        </div>

        <div class="relative h-80">
            <canvas id="vehicleUsageChart"></canvas>
        </div>
    </div>

    {{-- Tabla Detallada --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
            <flux:heading size="lg">Detalle por Vehículo</flux:heading>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Vehículo
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Servicios
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Horas de Uso
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Tasa Ocupación
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Ingresos
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($vehiculos as $vehiculo)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-6 py-4 text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $vehiculo['vehiculo'] }}
                            </td>
                            <td class="px-6 py-4 text-sm text-center text-zinc-900 dark:text-white">
                                <flux:badge variant="info">
                                    {{ $vehiculo['servicios'] }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 text-sm text-center text-zinc-600 dark:text-zinc-400">
                                {{ $vehiculo['horas_uso'] }}h
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-20 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                        <div 
                                            class="h-full transition-all duration-500
                                                {{ $vehiculo['tasa_ocupacion'] >= 80 ? 'bg-green-500' : 
                                                   ($vehiculo['tasa_ocupacion'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                            style="width: {{ $vehiculo['tasa_ocupacion'] }}%"
                                        ></div>
                                    </div>
                                    <span class="text-xs font-medium text-zinc-900 dark:text-white">
                                        {{ $vehiculo['tasa_ocupacion'] }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-right font-medium text-zinc-900 dark:text-white">
                                ${{ number_format($vehiculo['ingresos']) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top 3 Vehículos --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $topVehiculos = collect($vehiculos)->sortByDesc('ingresos')->take(3);
        @endphp

        @foreach($topVehiculos as $index => $vehiculo)
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-6 border border-blue-200 dark:border-blue-800">
                <div class="flex items-center gap-3 mb-4">
                    <div class="size-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                        #{{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Top {{ $index + 1 }}</div>
                        <div class="font-semibold text-zinc-900 dark:text-white truncate">
                            {{ $vehiculo['vehiculo'] }}
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-600 dark:text-zinc-400">Servicios:</span>
                        <span class="font-semibold text-zinc-900 dark:text-white">{{ $vehiculo['servicios'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-600 dark:text-zinc-400">Ingresos:</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">
                            ${{ number_format($vehiculo['ingresos']) }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:initialized', () => {
    const ctx = document.getElementById('vehicleUsageChart');
    if (ctx) {
        const chart = new Chart(ctx, {
            type: 'bar',
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
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>
@endpush