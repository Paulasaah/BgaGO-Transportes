import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

Alpine.plugin(intersect);
window.Alpine = Alpine;
Alpine.start();

import Chart from 'chart.js/auto';

// Hacer Chart disponible globalmente para Livewire
window.Chart = Chart;
