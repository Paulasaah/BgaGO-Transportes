// resources/js/app.js

// ============================
// 📊 Librerías principales
// ============================
import Chart from 'chart.js/auto'
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'
import Alpine from 'alpinejs'
import { createIcons, icons } from 'lucide'

// ============================
// 📍 Fix para íconos de Leaflet con Vite
// ============================
import icon from 'leaflet/dist/images/marker-icon.png'
import iconShadow from 'leaflet/dist/images/marker-shadow.png'
import iconRetina from 'leaflet/dist/images/marker-icon-2x.png'

delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
  iconUrl: icon,
  iconRetinaUrl: iconRetina,
  shadowUrl: iconShadow,
})

// ============================
// ⚡ Inicializar Alpine.js
// ============================
window.Alpine = Alpine
Alpine.start()

// ============================
// 🧭 Inicializar íconos Lucide
// ============================
function initLucideIcons() {
  createIcons({ icons })
}

// Primera carga del DOM
document.addEventListener('DOMContentLoaded', initLucideIcons)

// Compatibilidad con Livewire (v2 y v3)
document.addEventListener('livewire:navigated', initLucideIcons)
document.addEventListener('livewire:load', initLucideIcons)
document.addEventListener('livewire:update', initLucideIcons)

// ============================
// 🌍 Exportar globalmente
// ============================
window.Chart = Chart
window.L = L
