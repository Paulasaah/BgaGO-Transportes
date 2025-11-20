<x-layouts.app>
<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8" wire:poll.30s="refresh">

    {{-- Sistema de notificaciones --}}
    <x-dashboard.notification-toast />

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Dashboard de Administrador</flux:heading>
            <flux:subheading>Resumen general de BgaGo • Actualización automática cada 30s</flux:subheading>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-zinc-500 dark:text-zinc-400">
                Última actualización: {{ now()->format('H:i:s') }}
            </span>
            <flux:button wire:click="refresh" icon="arrow-path" variant="ghost" size="sm">
                Actualizar
            </flux:button>
        </div>
    </div>

    {{-- Tarjetas de estadísticas --}}
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4 flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M16 4H9.5a2 2 0 00-1.8 1H8a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V6a2 2 0 00-2-2z"/></svg>
            </div>
            <div class="space-y-0.5">
                <div class="text-sm text-zinc-600 dark:text-zinc-400">Reservas Activas</div>
                <div class="text-xl font-bold text-zinc-900 dark:text-white">{{ $stats['reservas_activas']['value'] }}</div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $stats['reservas_activas']['change'] }} {{ $stats['reservas_activas']['change_text'] }}</div>
            </div>
        </div>

        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4 flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7h11v8H2z"/><path d="M13 10h4l3 3v2h-7z"/><circle cx="5.5" cy="17.5" r="2"/><circle cx="16.5" cy="17.5" r="2"/></svg>
            </div>
            <div class="space-y-0.5">
                <div class="text-sm text-zinc-600 dark:text-zinc-400">Domicilios Hoy</div>
                <div class="text-xl font-bold text-zinc-900 dark:text-white">{{ $stats['domicilios_hoy']['value'] }}</div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $stats['domicilios_hoy']['change'] }} {{ $stats['domicilios_hoy']['change_text'] }}</div>
            </div>
        </div>

        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4 flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2v18H3"/><path d="M8 9h2v12H8"/><path d="M13 5h2v16h-2"/><path d="M18 12h2v9h-2"/></svg>
            </div>
            <div class="space-y-0.5">
                <div class="text-sm text-zinc-600 dark:text-zinc-400">Ingresos del Mes</div>
                <div class="text-xl font-bold text-zinc-900 dark:text-white">{{ $stats['ingresos_mes']['value'] }}</div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $stats['ingresos_mes']['change'] }} {{ $stats['ingresos_mes']['change_text'] }}</div>
            </div>
        </div>

        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4 flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 11-5.4 5.4L3 18.99 5.01 21l6.3-6.3a4 4 0 005.4-5.4l-2.01-2.01z"/></svg>
            </div>
            <div class="space-y-0.5">
                <div class="text-sm text-zinc-600 dark:text-zinc-400">En Mantenimiento</div>
                <div class="text-xl font-bold text-zinc-900 dark:text-white">{{ $stats['en_mantenimiento']['value'] }}</div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $stats['en_mantenimiento']['change'] }} {{ $stats['en_mantenimiento']['change_text'] }}</div>
            </div>
        </div>
    </div>

    {{-- Fila 1: Alertas, Telemetría y Servicios Activos --}}
    <div class="grid gap-6 lg:grid-cols-3">
        <livewire:dashboard.alerts-widget />
        <livewire:dashboard.telemetry-widget />
        <livewire:dashboard.active-services-widget />
    </div>

    {{-- Fila 2: Actividad Reciente --}}
    <livewire:dashboard.recent-activity-widget />

    {{-- Gráficos --}}
    <div class="grid gap-6 md:grid-cols-2">
        {{-- Gráfico de Reservas --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <flux:heading size="lg" class="mb-4">Reservas por Mes</flux:heading>
            <div style="position: relative; height: 300px;">
                <canvas id="reservasChart" wire:ignore></canvas>
            </div>
        </div>

        {{-- Gráfico de Distribución --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <flux:heading size="lg" class="mb-4">Distribución por Sede</flux:heading>
            <div style="position: relative; height: 300px;">
                <canvas id="distribucionChart" wire:ignore></canvas>
            </div>
        </div>
    </div>

    {{-- Accesos Rápidos --}}
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
        <flux:heading size="lg" class="mb-4">Accesos Rápidos</flux:heading>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <x-dashboard.quick-action
                :href="route('admin.vehicles.index')"
                icon="cube"
                label="Vehículos"
            />

            <x-dashboard.quick-action
                :href="route('admin.users.index')"
                icon="users"
                label="Usuarios"
            />

            <x-dashboard.quick-action
                :href="route('admin.drivers.index')"
                icon="user-circle"
                label="Conductores"
            />

            <x-dashboard.quick-action
                :href="route('admin.reservations.index')"
                icon="clipboard-document-list"
                label="Reservas"
            />

            <x-dashboard.quick-action
                :href="route('admin.map')"
                icon="map"
                label="Mapa en Vivo"
            />
        </div>
    </div>

    @push('scripts')
    <script>
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#e5e7eb' : '#374151';
        const gridColor = isDark ? '#374151' : '#e5e7eb';

        // === Gráfico de Reservas ===
        const ctxReservas = document.getElementById('reservasChart');
        if (ctxReservas) {
            new Chart(ctxReservas, {
                type: 'line',
                data: {
                    labels: @js($reservasLabels),
                    datasets: [{
                        label: 'Reservas',
                        data: @js($reservasData),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(59, 130, 246)',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: textColor,
                                usePointStyle: true,
                                padding: 15
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: textColor },
                            grid: { color: gridColor }
                        },
                        x: {
                            ticks: { color: textColor },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // === Gráfico de Distribución ===
        const ctxDistribucion = document.getElementById('distribucionChart');
        if (ctxDistribucion) {
            new Chart(ctxDistribucion, {
                type: 'doughnut',
                data: {
                    labels: @js($distribucionLabels),
                    datasets: [{
                        data: @js($distribucionData),
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',   // Azul
                            'rgba(16, 185, 129, 0.8)',   // Verde
                            'rgba(245, 158, 11, 0.8)',   // Amarillo
                            'rgba(239, 68, 68, 0.8)'     // Rojo
                        ],
                        borderColor: isDark ? '#1f2937' : '#fff',
                        borderWidth: 2,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: textColor,
                                padding: 20,
                                usePointStyle: true,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            backgroundColor: isDark ? '#1f2937' : '#fff',
                            titleColor: textColor,
                            bodyColor: textColor,
                            borderColor: isDark ? '#374151' : '#e5e7eb',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
    @endpush
</div>
</x-layouts.app>
