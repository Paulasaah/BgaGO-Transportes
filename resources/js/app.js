import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

Alpine.plugin(intersect);
window.Alpine = Alpine;
Alpine.start();

import Chart from 'chart.js/auto';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import { createIcons, icons } from 'lucide';

// Hacer Chart disponible globalmente para Livewire
window.Chart = Chart;
