<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Reportes y Análisis</flux:heading>
            <flux:subheading>Visualiza el rendimiento y métricas clave del negocio</flux:subheading>
        </div>

        <div class="flex gap-3">
            {{-- Selector de Período --}}
            <flux:select wire:model.live="periodo" class="w-40">
                <option value="mes">Este Mes</option>
                <option value="trimestre">Este Trimestre</option>
                <option value="año">Este Año</option>
            </flux:select>

            {{-- Botones de Exportación --}}
            <flux:dropdown>
                <flux:button variant="primary" icon="arrow-down-tray">
                    Exportar
                </flux:button>

                <flux:menu>
                    <flux:menu.item wire:click="exportReport('pdf')" icon="document">
                        Exportar a PDF
                    </flux:menu.item>
                    <flux:menu.item wire:click="exportReport('excel')" icon="table-cells">
                        Exportar a Excel
                    </flux:menu.item>
                    <flux:menu.item wire:click="exportReport('csv')" icon="document-text">
                        Exportar a CSV
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Servicios --}}
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <flux:icon.cube class="size-8 opacity-80" />
                <flux:badge variant="light" size="sm">
                    {{ ucfirst($periodo) }}
                </flux:badge>
            </div>
            <div class="text-3xl font-bold mb-1">{{ $stats['total_servicios'] }}</div>
            <div class="text-sm opacity-90">Total de Servicios</div>
        </div>

        {{-- Total Ingresos --}}
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <flux:icon.currency-dollar class="size-8 opacity-80" />
                <flux:badge variant="light" size="sm">
                    @if(isset($ingresos['cambio_porcentual']) && $ingresos['cambio_porcentual'] > 0)
                        +{{ $ingresos['cambio_porcentual'] }}%
                    @endif
                </flux:badge>
            </div>
            <div class="text-3xl font-bold mb-1">
                ${{ number_format($stats['total_ingresos'] / 1000000, 1) }}M
            </div>
            <div class="text-sm opacity-90">Ingresos Totales</div>
        </div>

        {{-- Calificación Promedio --}}
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <flux:icon.star class="size-8 opacity-80" />
                <flux:badge variant="light" size="sm">Excelente</flux:badge>
            </div>
            <div class="text-3xl font-bold mb-1">{{ $stats['promedio_calificacion'] }}</div>
            <div class="text-sm opacity-90">Calificación Promedio</div>
        </div>

        {{-- Tasa de Cancelación --}}
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <flux:icon.chart-bar class="size-8 opacity-80" />
                <flux:badge variant="light" size="sm">
                    @if($stats['tasa_cancelacion'] < 5)
                        Óptimo
                    @else
                        Revisar
                    @endif
                </flux:badge>
            </div>
            <div class="text-3xl font-bold mb-1">{{ $stats['tasa_cancelacion'] }}%</div>
            <div class="text-sm opacity-90">Tasa de Cancelación</div>
        </div>
    </div>

    {{-- Tabs de Reportes --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow border border-zinc-200 dark:border-zinc-700">
        <div class="border-b border-zinc-200 dark:border-zinc-700">
            <nav class="flex -mb-px">
                <button 
                    wire:click="$set('reporteSeleccionado', 'ingresos')"
                    class="px-6 py-4 text-sm font-medium border-b-2 transition-colors
                        {{ $reporteSeleccionado === 'ingresos' 
                            ? 'border-blue-500 text-blue-600 dark:text-blue-400' 
                            : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200' }}"
                >
                    <div class="flex items-center gap-2">
                        <flux:icon.currency-dollar class="size-5" />
                        <span>Reporte de Ingresos</span>
                    </div>
                </button>

                <button 
                    wire:click="$set('reporteSeleccionado', 'vehiculos')"
                    class="px-6 py-4 text-sm font-medium border-b-2 transition-colors
                        {{ $reporteSeleccionado === 'vehiculos' 
                            ? 'border-blue-500 text-blue-600 dark:text-blue-400' 
                            : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200' }}"
                >
                    <div class="flex items-center gap-2">
                        <flux:icon.truck class="size-5" />
                        <span>Uso de Vehículos</span>
                    </div>
                </button>

                <button 
                    wire:click="$set('reporteSeleccionado', 'conductores')"
                    class="px-6 py-4 text-sm font-medium border-b-2 transition-colors
                        {{ $reporteSeleccionado === 'conductores' 
                            ? 'border-blue-500 text-blue-600 dark:text-blue-400' 
                            : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200' }}"
                >
                    <div class="flex items-center gap-2">
                        <flux:icon.user class="size-5" />
                        <span>Desempeño de Conductores</span>
                    </div>
                </button>
            </nav>
        </div>

        <div class="p-6">
            @if($reporteSeleccionado === 'ingresos')
                <livewire:reports.revenue-report :periodo="$periodo" :key="'revenue-'.$periodo" />
            @elseif($reporteSeleccionado === 'vehiculos')
                <livewire:reports.vehicle-usage-report :periodo="$periodo" :key="'vehicles-'.$periodo" />
            @elseif($reporteSeleccionado === 'conductores')
                <livewire:reports.driver-performance-report :periodo="$periodo" :key="'drivers-'.$periodo" />
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush