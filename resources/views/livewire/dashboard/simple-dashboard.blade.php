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
        <x-stats.card
            title="Reservas Activas"
            :value="$stats['reservas_activas']['value']"
            :change="$stats['reservas_activas']['change'] . ' ' . $stats['reservas_activas']['change_text']"
            icon="clipboard-check"
            color="blue"
        />

        <x-stats.card
            title="Domicilios Hoy"
            :value="$stats['domicilios_hoy']['value']"
            :change="$stats['domicilios_hoy']['change'] . ' ' . $stats['domicilios_hoy']['change_text']"
            icon="truck"
            color="green"
        />

        <x-stats.card
            title="Ingresos del Mes"
            :value="$stats['ingresos_mes']['value']"
            :change="$stats['ingresos_mes']['change'] . ' ' . $stats['ingresos_mes']['change_text']"
            icon="chart-column"
            color="purple"
        />

        <x-stats.card
            title="En Mantenimiento"
            :value="$stats['en_mantenimiento']['value']"
            :change="$stats['en_mantenimiento']['change'] . ' ' . $stats['en_mantenimiento']['change_text']"
            icon="wrench"
            color="orange"
        />
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

    {{-- Scripts de Chart.js --}}
    @script
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
    @endscript
</div>
