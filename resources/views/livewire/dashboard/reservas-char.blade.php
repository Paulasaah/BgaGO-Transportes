<div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 border border-zinc-200 dark:border-zinc-700">
    <flux:heading size="lg" class="mb-4">Reservas por Mes</flux:heading>
    <div style="position: relative; height: 300px;">
        <canvas id="reservasChart" wire:ignore></canvas>
    </div>
</div>

@script
<script>
    let reservasChart = null;
    
    const initReservasChart = () => {
        const ctx = document.getElementById('reservasChart');
        if (!ctx) return;
        
        // Destruir gráfico anterior si existe
        if (reservasChart) {
            reservasChart.destroy();
        }
        
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#e5e7eb' : '#374151';
        const gridColor = isDark ? '#374151' : '#e5e7eb';
        
        reservasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @js($chartData['labels']),
                datasets: [{
                    label: 'Reservas',
                    data: @js($chartData['data']),
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
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: textColor,
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: isDark ? '#1f2937' : '#fff',
                        titleColor: textColor,
                        bodyColor: textColor,
                        borderColor: gridColor,
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: textColor,
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: gridColor,
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            color: textColor,
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    };
    
    // Inicializar cuando el componente está listo
    initReservasChart();
    
    // Reinicializar cuando Livewire actualiza el componente
    $wire.on('chartUpdated', () => {
        initReservasChart();
    });
</script>
@endscript