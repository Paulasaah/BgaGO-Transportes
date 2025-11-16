import Chart from 'chart.js/auto';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import { createIcons, icons } from 'lucide';

// Laravel Echo para broadcasting en tiempo real
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Fix para los iconos de Leaflet con Vite
import icon from 'leaflet/dist/images/marker-icon.png';
import iconShadow from 'leaflet/dist/images/marker-shadow.png';
import iconRetina from 'leaflet/dist/images/marker-icon-2x.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconUrl: icon,
    iconRetinaUrl: iconRetina,
    shadowUrl: iconShadow,
});

// Configurar Laravel Echo con Reverb
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Función para inicializar iconos
function initLucideIcons() {
    createIcons({ icons });
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initLucideIcons);

// Re-inicializar después de navegación de Livewire (v3)
document.addEventListener('livewire:navigated', initLucideIcons);

// Re-inicializar después de carga de Livewire (v2)
document.addEventListener('livewire:load', initLucideIcons);

// Re-inicializar después de actualizaciones de Livewire
document.addEventListener('livewire:update', initLucideIcons);

// Hacer disponibles globalmente
window.Chart = Chart;
window.L = L;