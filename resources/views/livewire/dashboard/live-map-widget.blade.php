<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                <svg class="size-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>
            <div>
                <flux:heading size="lg">Mapa en Vivo</flux:heading>
                <flux:subheading>Ubicación de vehículos en tiempo real</flux:subheading>
            </div>
        </div>
        
        <button 
            wire:click="viewFullMap"
            class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
            Ver mapa completo →
        </button>
    </div>

    {{-- Estadísticas rápidas --}}
    <div class="grid grid-cols-4 gap-3 mb-4">
        <div class="text-center p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
            <div class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['total'] }}</div>
            <div class="text-xs text-zinc-600 dark:text-zinc-400">Total</div>
        </div>
        <div class="text-center p-3 rounded-lg bg-blue-50 dark:bg-blue-900/10">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['active'] }}</div>
            <div class="text-xs text-zinc-600 dark:text-zinc-400">Activos</div>
        </div>
        <div class="text-center p-3 rounded-lg bg-green-50 dark:bg-green-900/10">
            <div class="text-2xl font-bold text-green-600">{{ $stats['available'] }}</div>
            <div class="text-xs text-zinc-600 dark:text-zinc-400">Disponibles</div>
        </div>
        <div class="text-center p-3 rounded-lg bg-orange-50 dark:bg-orange-900/10">
            <div class="text-2xl font-bold text-orange-600">{{ $stats['maintenance'] }}</div>
            <div class="text-xs text-zinc-600 dark:text-zinc-400">Mantenim.</div>
        </div>
    </div>

    {{-- Mapa --}}
    <div class="relative rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-800" style="height: 400px;">
        <div id="liveMap" wire:ignore class="w-full h-full"></div>
        
        {{-- Overlay de carga --}}
        <div id="mapLoading" class="absolute inset-0 bg-white dark:bg-zinc-900 flex items-center justify-center">
            <div class="text-center">
                <svg class="animate-spin size-8 text-blue-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Cargando mapa...</p>
            </div>
        </div>
    </div>

    {{-- Leyenda --}}
    <div class="mt-4 flex items-center justify-center gap-6 text-xs">
        <div class="flex items-center gap-2">
            <div class="size-3 rounded-full bg-blue-500"></div>
            <span class="text-zinc-600 dark:text-zinc-400">En servicio</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="size-3 rounded-full bg-green-500"></div>
            <span class="text-zinc-600 dark:text-zinc-400">Disponible</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="size-3 rounded-full bg-orange-500"></div>
            <span class="text-zinc-600 dark:text-zinc-400">Mantenimiento</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="size-3 rounded-full bg-zinc-400"></div>
            <span class="text-zinc-600 dark:text-zinc-400">Offline</span>
        </div>
    </div>

    {{-- Script de Leaflet --}}
    @script
    <script>
        let liveMap = null;
        let markers = {};
        let branchCircles = {};

        function initLiveMap() {
            // Ocultar loading
            document.getElementById('mapLoading').style.display = 'none';

            // Centro de Colombia (Bucaramanga aproximadamente)
            const center = [7.1193, -73.1227];
            
            // Inicializar mapa
            liveMap = L.map('liveMap', {
                zoomControl: true,
                attributionControl: false
            }).setView(center, 13);

            // Agregar tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(liveMap);

            // Agregar sedes
            const branches = @js($branches);
            branches.forEach(branch => {
                if (branch.lat && branch.lng) {
                    // Círculo de cobertura
                    const circle = L.circle([branch.lat, branch.lng], {
                        color: '#3b82f6',
                        fillColor: '#3b82f6',
                        fillOpacity: 0.1,
                        radius: branch.radio
                    }).addTo(liveMap);

                    // Marcador de sede
                    const marker = L.marker([branch.lat, branch.lng], {
                        icon: L.divIcon({
                            className: 'custom-div-icon',
                            html: `<div class="flex items-center justify-center size-8 rounded-full bg-blue-600 text-white border-2 border-white shadow-lg">
                                    <svg class="size-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                                    </svg>
                                   </div>`,
                            iconSize: [32, 32],
                            iconAnchor: [16, 16]
                        })
                    }).addTo(liveMap);

                    marker.bindPopup(`<b>${branch.nombre}</b><br>Sede`);
                    
                    branchCircles[branch.id] = { circle, marker };
                }
            });

            // Agregar vehículos
            updateVehicleMarkers();
        }

        function updateVehicleMarkers() {
            const vehicles = @js($vehicles);
            
            // Limpiar marcadores antiguos
            Object.values(markers).forEach(marker => marker.remove());
            markers = {};

            // Agregar nuevos marcadores
            vehicles.forEach(vehicle => {
                if (vehicle.lat && vehicle.lng) {
                    const color = getVehicleColor(vehicle.status);
                    
                    const marker = L.marker([vehicle.lat, vehicle.lng], {
                        icon: L.divIcon({
                            className: 'custom-div-icon',
                            html: `<div class="relative">
                                    <div class="flex items-center justify-center size-10 rounded-full bg-${color}-500 text-white border-2 border-white shadow-lg">
                                        <svg class="size-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                                        </svg>
                                    </div>
                                    ${vehicle.status === 'busy' ? '<div class="absolute -top-1 -right-1 size-3 bg-red-500 rounded-full animate-pulse"></div>' : ''}
                                   </div>`,
                            iconSize: [40, 40],
                            iconAnchor: [20, 20]
                        })
                    }).addTo(liveMap);

                    const popupContent = `
                        <div class="p-2">
                            <b>${vehicle.placa}</b><br>
                            ${vehicle.conductor ? `Conductor: ${vehicle.conductor}<br>` : ''}
                            ${vehicle.velocidad ? `Velocidad: ${vehicle.velocidad} km/h<br>` : ''}
                            Estado: ${getStatusLabel(vehicle.status)}<br>
                            ${vehicle.servicio_activo ? `Servicio: ${vehicle.servicio_activo}` : ''}
                        </div>
                    `;
                    
                    marker.bindPopup(popupContent);
                    markers[vehicle.id] = marker;
                }
            });

            // Ajustar vista si hay vehículos
            if (vehicles.length > 0) {
                const bounds = vehicles
                    .filter(v => v.lat && v.lng)
                    .map(v => [v.lat, v.lng]);
                
                if (bounds.length > 0) {
                    liveMap.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
                }
            }
        }

        function getVehicleColor(status) {
            switch(status) {
                case 'busy': return 'blue';
                case 'available': return 'green';
                case 'maintenance': return 'orange';
                default: return 'zinc';
            }
        }

        function getStatusLabel(status) {
            switch(status) {
                case 'busy': return 'En servicio';
                case 'available': return 'Disponible';
                case 'maintenance': return 'Mantenimiento';
                case 'offline': return 'Desconectado';
                default: return 'Desconocido';
            }
        }

        // Inicializar cuando el componente esté listo
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof L !== 'undefined') {
                initLiveMap();
            }
        });

        // Actualizar cuando se recarguen los datos
        Livewire.on('refreshDashboard', () => {
            if (liveMap) {
                updateVehicleMarkers();
            }
        });
    </script>
    @endscript
</div>
