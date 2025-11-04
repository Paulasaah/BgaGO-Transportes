<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        <!-- Header -->
        <div>
            <flux:heading size="xl">Dashboard de Administrador</flux:heading>
            <flux:subheading>Resumen general de BgaGo</flux:subheading>
        </div>

        <!-- Tarjetas de estadísticas -->
        <x-dashboard.stats-grid>
            <x-stats.card
                title="Reservas Activas"
                value="24"
                change="+5 desde ayer"
                icon="clipboard-document-list"
                color="blue"
            />

            <x-stats.card
                title="Domicilios Hoy"
                value="18"
                change="+3 en progreso"
                icon="truck"
                color="green"
            />

            <x-stats.card
                title="Ingresos del Mes"
                value="$4.2M"
                change="+12% vs mes anterior"
                icon="currency-dollar"
                color="purple"
            />

            <x-stats.card
                title="En Mantenimiento"
                value="5"
                change="2 requieren atención"
                changeType="neutral"
                icon="wrench-screwdriver"
                color="amber"
            />
        </x-dashboard.stats-grid>

        <!-- Gráficos -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Gráfico de Reservas -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 border border-zinc-200 dark:border-zinc-700">
                <flux:heading size="lg" class="mb-4">Reservas por Mes</flux:heading>
                <div style="position: relative; height: 300px;">
                    <canvas id="reservasChart"></canvas>
                </div>
            </div>

            <!-- Gráfico de Distribución -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 border border-zinc-200 dark:border-zinc-700">
                <flux:heading size="lg" class="mb-4">Distribución por Sede</flux:heading>
                <div style="position: relative; height: 300px;">
                    <canvas id="distribucionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow border border-zinc-200 dark:border-zinc-700 p-6">
            <flux:heading size="lg" class="mb-4">Accesos Rápidos</flux:heading>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <x-dashboard.quick-action
                    href="/admin/vehicles"
                    icon="cube"
                    label="Gestión de Medios"
                />

                <x-dashboard.quick-action
                    href="/admin/users"
                    icon="users"
                    label="Usuarios"
                />

                <x-dashboard.quick-action
                    href="/admin/drivers"
                    icon="user-circle"
                    label="Conductores"
                />

                <x-dashboard.quick-action
                    href="/monitoreo"
                    icon="map"
                    label="Monitoreo"
                />
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function renderCharts() {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#e5e7eb' : '#374151';
            const gridColor = isDark ? '#374151' : '#e5e7eb';

            // --- Gráfico de Reservas ---
            const ctxReservas = document.getElementById('reservasChart');
            if (ctxReservas) {
                if (window.reservasChart instanceof Chart) {
                    window.reservasChart.destroy();
                }

                window.reservasChart = new Chart(ctxReservas, {
                    type: 'line',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                        datasets: [{
                            label: 'Reservas',
                            data: [65, 78, 90, 81, 95, 103, 110, 98, 115, 122, 130, 140],
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

            // --- Gráfico de Distribución ---
            const ctxDistribucion = document.getElementById('distribucionChart');
            if (ctxDistribucion) {
                if (window.distribucionChart instanceof Chart) {
                    window.distribucionChart.destroy();
                }

                window.distribucionChart = new Chart(ctxDistribucion, {
                    type: 'doughnut',
                    data: {
                        labels: ['Sede Norte', 'Sede Sur', 'Sede Centro', 'Sede Oriente'],
                        datasets: [{
                            data: [35, 28, 22, 15],
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(239, 68, 68, 0.8)'
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
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
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
        }

        // Estos eventos hacen que se recarguen los gráficos al navegar
        document.addEventListener('livewire:load', renderCharts);
        document.addEventListener('livewire:navigated', renderCharts);
    </script>
    @endpush

</x-layouts.app>