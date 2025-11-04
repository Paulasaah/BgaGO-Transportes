<div class="flex flex-col gap-4 h-full">
    <!-- Barra de Controles -->
    <div class="flex-shrink-0 bg-white dark:bg-zinc-800 rounded-lg shadow border border-zinc-200 dark:border-zinc-700 p-4">
        <div class="flex flex-wrap items-center gap-4">
            <!-- Filtros -->
            <div class="flex items-center gap-3">
                <flux:select wire:model.live="selectedSede" placeholder="Todas las sedes" class="w-48">
                    <option value="">Todas las sedes</option>
                    @foreach($filters['sedes'] ?? [] as $sede)
                        <option value="{{ $sede }}">{{ $sede }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="selectedStatus" placeholder="Todos los estados" class="w-48">
                    <option value="">Todos los estados</option>
                    @foreach($filters['estados'] ?? [] as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </flux:select>

                @if($selectedSede || $selectedStatus)
                    <flux:button wire:click="clearFilters" variant="ghost" size="sm">
                        <flux:icon.x-mark class="size-4" />
                        Limpiar
                    </flux:button>
                @endif
            </div>

            <flux:separator vertical class="h-8" />

            <!-- Controles -->
            <div class="flex items-center gap-3">
                <flux:button 
                wire:click="refreshMap" 
                icon="arrow-path" 
                variant="ghost" 
                size="sm"
                >
                    Actualizar
                </flux:button>

                <flux:button 
                    wire:click="toggleAutoRefresh" 
                    icon="arrow-path"
                    :variant="$autoRefresh ? 'primary' : 'ghost'" 
                    size="sm"
                >
                    Auto ({{ $refreshInterval }}s)
                </flux:button>
            </div>

            <flux:spacer />

            <!-- Estadísticas -->
            <div class="flex items-center gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <div class="size-3 rounded-full bg-green-500"></div>
                    <span class="text-zinc-600 dark:text-zinc-400">
                        {{ $vehiclesCount }} vehículos
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="size-3 rounded-full bg-blue-500"></div>
                    <span class="text-zinc-600 dark:text-zinc-400">
                        {{ $activeRoutesCount }} rutas activas
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor del Mapa y Panel Lateral -->
    <div class="flex-1 flex gap-4 min-h-0">
        <!-- Mapa -->
        <div class="flex-1 bg-white dark:bg-zinc-800 rounded-lg shadow border border-zinc-200 dark:border-zinc-700 overflow-hidden relative">
            <div id="map" wire:ignore style="width: 100%; height: 100%; min-height: 400px;"></div>
        </div>

        <!-- Panel Lateral de Vehículos -->
        <div class="w-80 bg-white dark:bg-zinc-800 rounded-lg shadow border border-zinc-200 dark:border-zinc-700 overflow-hidden flex flex-col">
            <div class="flex-shrink-0 p-4 border-b border-zinc-200 dark:border-zinc-700">
                <flux:heading size="lg">Vehículos en Tiempo Real</flux:heading>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                @forelse($vehicles as $vehicle)
                    <div 
                        wire:click="focusVehicle({{ $vehicle['id'] }})"
                        class="p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-blue-500 dark:hover:border-blue-500 cursor-pointer transition-all"
                    >
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <div class="font-semibold text-zinc-900 dark:text-white">
                                    {{ $vehicle['placa'] }}
                                </div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $vehicle['conductor'] ?? 'Sin conductor' }}
                                </div>
                            </div>
                            <x-badges.status :status="$vehicle['status']" size="sm" />
                        </div>

                        <div class="flex items-center gap-4 text-xs text-zinc-600 dark:text-zinc-400">
                            <div class="flex items-center gap-1">
                                <flux:icon.arrow-trending-up class="size-3" />
                                {{ $vehicle['velocidad'] }} km/h
                            </div>
                            <div class="flex items-center gap-1">
                                <flux:icon.map-pin class="size-3" />
                                {{ $vehicle['rumbo'] }}
                            </div>
                        </div>

                        @if(isset($vehicle['servicio_activo']))
                            <div class="mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                                <div class="text-xs text-blue-600 dark:text-blue-400">
                                    <flux:icon.truck class="size-3 inline" />
                                    {{ $vehicle['servicio_activo'] }}
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                        <flux:icon.map class="size-12 mx-auto mb-2 opacity-50" />
                        <p>No hay vehículos que mostrar</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Leyenda del Mapa -->
    <div class="flex-shrink-0 bg-white dark:bg-zinc-800 rounded-lg shadow border border-zinc-200 dark:border-zinc-700 p-4">
        <div class="flex items-center gap-6 text-sm">
            <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">Leyenda:</flux:heading>
            
            <div class="flex items-center gap-2">
                <div class="size-4 rounded-full bg-green-500"></div>
                <span>Disponible</span>
            </div>
            
            <div class="flex items-center gap-2">
                <div class="size-4 rounded-full bg-yellow-500"></div>
                <span>Ocupado</span>
            </div>
            
            <div class="flex items-center gap-2">
                <div class="size-4 rounded-full bg-orange-500"></div>
                <span>Mantenimiento</span>
            </div>
            
            <div class="flex items-center gap-2">
                <div class="w-8 h-1 bg-blue-500"></div>
                <span>Ruta Activa</span>
            </div>
        </div>
    </div>
</div>

@assets
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endassets

@script
<script>
    let map = null;
    let vehicleMarkers = {};
    let routePolylines = {};
    let autoRefreshTimer = null;

    const initMap = () => {
        if (map) return;

        const mapElement = document.getElementById('map');
        if (!mapElement) {
            console.error('Map element not found');
            return;
        }

        try {
            // Centrar en Bucaramanga
            map = L.map('map').setView([7.1193, -73.1227], 13);

            // Agregar capa de tiles (OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Forzar actualización del tamaño del mapa
            setTimeout(() => {
                map.invalidateSize();
            }, 100);

            // Cargar datos iniciales
            updateMapData(@js($vehicles), @js($routes));
        } catch (error) {
            console.error('Error initializing map:', error);
        }
    };

    const updateMapData = (vehicles, routes) => {
        if (!map) return;

        // Limpiar marcadores anteriores
        Object.values(vehicleMarkers).forEach(marker => marker.remove());
        vehicleMarkers = {};

        // Limpiar rutas anteriores
        Object.values(routePolylines).forEach(polyline => polyline.remove());
        routePolylines = {};

        // Agregar marcadores de vehículos
        vehicles.forEach(vehicle => {
            const color = getStatusColor(vehicle.status);
            const icon = L.divIcon({
                className: 'custom-marker',
                html: `
                    <div style="
                        background-color: ${color};
                        width: 30px;
                        height: 30px;
                        border-radius: 50%;
                        border: 3px solid white;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-weight: bold;
                        font-size: 10px;
                    ">
                        ${vehicle.placa.substring(0, 3)}
                    </div>
                `,
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });

            const marker = L.marker([vehicle.lat, vehicle.lng], { icon })
                .addTo(map)
                .bindPopup(`
                    <div class="text-sm">
                        <strong>${vehicle.placa}</strong><br>
                        Conductor: ${vehicle.conductor || 'N/A'}<br>
                        Velocidad: ${vehicle.velocidad} km/h<br>
                        Estado: ${vehicle.status}<br>
                        ${vehicle.servicio_activo ? `Servicio: ${vehicle.servicio_activo}` : ''}
                    </div>
                `);

            vehicleMarkers[vehicle.id] = marker;
        });

        // Agregar rutas activas
        routes.forEach(route => {
            const points = [
                [route.origen.lat, route.origen.lng],
                ...route.waypoints.map(wp => [wp.lat, wp.lng]),
                [route.destino.lat, route.destino.lng]
            ];

            const polyline = L.polyline(points, {
                color: '#3b82f6',
                weight: 4,
                opacity: 0.7,
                dashArray: '10, 10'
            }).addTo(map);

            // Marcador de origen
            L.circleMarker([route.origen.lat, route.origen.lng], {
                radius: 8,
                fillColor: '#10b981',
                color: '#fff',
                weight: 2,
                fillOpacity: 1
            }).addTo(map).bindPopup(`<strong>Origen:</strong> ${route.origen.nombre}`);

            // Marcador de destino
            L.circleMarker([route.destino.lat, route.destino.lng], {
                radius: 8,
                fillColor: '#ef4444',
                color: '#fff',
                weight: 2,
                fillOpacity: 1
            }).addTo(map).bindPopup(`<strong>Destino:</strong> ${route.destino.nombre}`);

            routePolylines[route.servicio_id] = polyline;
        });

        // Ajustar zoom para mostrar todos los marcadores
        if (vehicles.length > 0) {
            const bounds = L.latLngBounds(vehicles.map(v => [v.lat, v.lng]));
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    };

    const getStatusColor = (status) => {
        const colors = {
            'available': '#10b981',
            'busy': '#f59e0b',
            'maintenance': '#f97316'
        };
        return colors[status] || '#6b7280';
    };

    // Inicializar mapa cuando el componente está listo
    setTimeout(() => {
        initMap();
    }, 100);

    // Manejar redimensionamiento de ventana
    window.addEventListener('resize', () => {
        if (map) {
            setTimeout(() => {
                map.invalidateSize();
            }, 100);
        }
    });

    // Escuchar eventos de Livewire
    $wire.on('mapDataUpdated', (event) => {
        if (map) {
            updateMapData(event.vehicles, event.routes);
        }
    });

    $wire.on('focusOnVehicle', (event) => {
        if (map && vehicleMarkers[event.vehicle.id]) {
            map.setView([event.vehicle.lat, event.vehicle.lng], 16);
            vehicleMarkers[event.vehicle.id].openPopup();
        }
    });

    $wire.on('startAutoRefresh', (event) => {
        if (autoRefreshTimer) clearInterval(autoRefreshTimer);
        
        autoRefreshTimer = setInterval(() => {
            $wire.refreshMap();
        }, event.interval * 1000);
    });

    $wire.on('stopAutoRefresh', () => {
        if (autoRefreshTimer) {
            clearInterval(autoRefreshTimer);
            autoRefreshTimer = null;
        }
    });

    $wire.on('refreshNotification', () => {
        console.log('Mapa actualizado');
    });

    // Limpiar al destruir el componente
    document.addEventListener('livewire:navigating', () => {
        if (autoRefreshTimer) clearInterval(autoRefreshTimer);
        if (map) {
            map.remove();
            map = null;
        }
    });
</script>
@endscript