<div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 border border-zinc-200 dark:border-zinc-700">
    <flux:heading size="lg" class="mb-4">Distribución por Sede</flux:heading>
    <div style="position: relative; height: 300px;">
        <canvas id="distribucionChart" wire:ignore></canvas>
    </div>
</div>

@script
<script>
    let distribucionChart = null;
    
    const initDistribucionChart = () => {
        const ctx = document.getElementById('distribucionChart');
        if (!ctx) return;
        
        if (distribucionChart) {
            distribucionChart.destroy();
        }
        
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#e5e7eb' : '#374151';
        
        distribucionChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @js($chartData['labels']),
                datasets: [{
                    data: @js($chartData['data']),
                    backgroundColor: @js($chartData['colors']),
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
                            font: {
                                size: 12
                            }
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
    };
    
    initDistribucionChart();
    
    $wire.on('chartUpdated', () => {
        initDistribucionChart();
    });
</script>
@endscript