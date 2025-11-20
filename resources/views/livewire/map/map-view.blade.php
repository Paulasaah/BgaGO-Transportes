<div class="flex flex-col gap-4 h-full">
    {{-- Panel de Estadísticas Superior --}}
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-3 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2.5">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <x-icon name="radio" class="size-4 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <div class="text-xl font-bold text-zinc-900 dark:text-white">{{ $stats['total_devices'] }}</div>
                    <div class="text-xs text-zinc-600 dark:text-zinc-400">Dispositivos</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-3 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2.5">
                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <x-icon name="car" class="size-4 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <div class="text-xl font-bold text-zinc-900 dark:text-white">{{ $stats['active_vehicles'] }}</div>
                    <div class="text-xs text-zinc-600 dark:text-zinc-400">Vehículos activos</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-3 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2.5">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <x-icon name="bike" class="size-4 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <div class="text-xl font-bold text-zinc-900 dark:text-white">{{ $stats['active_conductors'] }}</div>
                    <div class="text-xs text-zinc-600 dark:text-zinc-400">Conductores activos</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-3 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2.5">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                    <x-icon name="battery-charging" class="size-4 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div>
                    <div class="text-xl font-bold text-zinc-900 dark:text-white">{{ $stats['charging'] }}</div>
                    <div class="text-xs text-zinc-600 dark:text-zinc-400">Cargando</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-3 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2.5">
                <div class="p-2 {{ $stats['maintenance_needed'] > 0 ? 'bg-red-100 dark:bg-red-900/30' : 'bg-zinc-100 dark:bg-zinc-700' }} rounded-lg">
                    <x-icon name="wrench" class="size-4 {{ $stats['maintenance_needed'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-400' }}" />
                </div>
                <div>
                    <div class="text-xl font-bold {{ $stats['maintenance_needed'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-600 dark:text-zinc-400' }}">
                        {{ $stats['maintenance_needed'] }}
                    </div>
                    <div class="text-xs text-zinc-600 dark:text-zinc-400">Mantenimiento</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-3 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-2.5">
                <div class="p-2 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg">
                    <x-icon name="zap" class="size-4 text-cyan-600 dark:text-cyan-400" />
                </div>
                <div>
                    <div class="text-xl font-bold text-zinc-900 dark:text-white">{{ $stats['avg_battery'] }}%</div>
                    <div class="text-xs text-zinc-600 dark:text-zinc-400">Batería promedio</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Barra de Controles --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-3">
        <div class="flex flex-wrap items-center gap-2.5">
            <button wire:click="refreshMap" 
                    class="flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium">
                <x-icon name="refresh-cw" class="size-3.5" />
                Actualizar
            </button>

            <button wire:click="toggleAutoRefresh" 
                    class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors text-sm font-medium
                           {{ $autoRefresh ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-700 dark:text-zinc-300' }}">
                <x-icon name="refresh-cw" class="size-3.5 {{ $autoRefresh ? 'animate-spin' : '' }}" />
                Auto {{ $autoRefresh ? 'ON' : 'OFF' }}
            </button>
            
            <div class="h-5 w-px bg-zinc-300 dark:bg-zinc-600"></div>
            
            <select wire:model.live="filterType" class="px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm">
                <option value="all">Todos</option>
                <option value="vehiculo">Vehículos</option>
                <option value="conductor">Conductores</option>
            </select>
            
            <select wire:model.live="filterStatus" class="px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm">
                <option value="all">Todos los estados</option>
                <option value="active">En ruta</option>
                <option value="idle">En espera</option>
                <option value="charging">Cargando</option>
                <option value="maintenance">Mantenimiento</option>
            </select>

            <label class="flex items-center gap-1.5 text-xs text-zinc-600 dark:text-zinc-300">
                <input type="checkbox" wire:model.live="showSimulated" class="rounded border-zinc-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500" />
                <span>Mostrar simulados</span>
            </label>

            <div class="flex-1"></div>

            <div class="flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                {{ now()->format('H:i:s') }}
            </div>
        </div>
    </div>

    {{-- Mapa y Panel Lateral --}}
    <div class="flex-1 flex gap-4 min-h-[600px]">
        {{-- Mapa Principal --}}
        <div class="flex-1 bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 relative overflow-hidden">
            <div id="map" wire:ignore style="width:100%;height:100%;min-height:600px;"></div>
            
            {{-- Leyenda --}}
            <div class="absolute top-3 left-3 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-3 z-[1000] text-xs">
                <div class="font-bold mb-2 text-zinc-900 dark:text-white flex items-center gap-1.5">
                    <x-icon name="info" class="size-3.5" />
                    Leyenda
                </div>
                
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                        <span class="text-zinc-600 dark:text-zinc-400">En ruta</span>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-gray-500"></div>
                        <span class="text-zinc-600 dark:text-zinc-400">En espera</span>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <span class="text-zinc-600 dark:text-zinc-400">Cargando</span>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <span class="text-zinc-600 dark:text-zinc-400">Mantenimiento</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel Lateral --}}
        <div class="w-80 bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden flex flex-col">
            {{-- Header --}}
            <div class="p-3 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                    <x-icon name="list" class="size-4" />
                    Dispositivos Activos
                </h3>
                <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">
                    {{ count($vehicles) }} dispositivo(s)
                </p>
            </div>

            {{-- Lista --}}
            <div class="flex-1 overflow-y-auto p-2 space-y-2">
                @forelse($vehicles as $vehicle)
                    <div wire:click="focusVehicle('{{ $vehicle['device_id'] }}')" 
                         class="p-2.5 rounded-lg cursor-pointer transition-all border
                                {{ $vehicle['status'] === 'active' ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 hover:bg-blue-100' : 
                                   ($vehicle['status'] === 'charging' ? 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800 hover:bg-yellow-100' : 
                                   ($vehicle['status'] === 'maintenance' ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 hover:bg-red-100' : 
                                   'bg-zinc-50 dark:bg-zinc-900/50 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100')) }}">
                        
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center
                                           {{ $vehicle['type'] === 'conductor' ? 'bg-purple-100 dark:bg-purple-900/30' : 'bg-blue-100 dark:bg-blue-900/30' }}">
                                    <x-icon name="{{ $vehicle['type'] === 'conductor' ? 'bike' : 'car' }}" 
                                            class="size-3.5 {{ $vehicle['type'] === 'conductor' ? 'text-purple-600' : 'text-blue-600' }}" />
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-zinc-900 dark:text-white">{{ $vehicle['device_id'] }}</div>
                                    @if($vehicle['type'] === 'conductor')
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">{{ $vehicle['driver_name'] }}</div>
                                    @endif
                                </div>
                            </div>
                            
                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                        {{ $vehicle['status'] === 'active' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 
                                           ($vehicle['status'] === 'charging' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300' : 
                                           ($vehicle['status'] === 'maintenance' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 
                                           'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300')) }}">
                                {{ $vehicle['status_label'] }}
                            </span>
                        </div>

                        {{-- Información --}}
                        <div class="space-y-1.5 text-xs">
                            {{-- Batería --}}
                            <div>
                                <div class="flex justify-between mb-0.5">
                                    <span class="text-zinc-600 dark:text-zinc-400">Batería</span>
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ number_format($vehicle['battery'], 1) }}%</span>
                                </div>
                                <div class="w-full h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300
                                               {{ $vehicle['battery'] >= 70 ? 'bg-green-500' : 
                                                  ($vehicle['battery'] >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                         style="width: {{ $vehicle['battery'] }}%"></div>
                                </div>
                            </div>

                            {{-- Salud --}}
                            <div>
                                <div class="flex justify-between mb-0.5">
                                    <span class="text-zinc-600 dark:text-zinc-400">Salud</span>
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ number_format($vehicle['battery_health'], 1) }}%</span>
                                </div>
                                <div class="w-full h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300
                                               {{ $vehicle['battery_health'] >= 80 ? 'bg-green-500' : 
                                                  ($vehicle['battery_health'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                         style="width: {{ $vehicle['battery_health'] }}%"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Detalles --}}
                        <div class="grid grid-cols-2 gap-1.5 mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-700 text-xs">
                            <div class="flex items-center gap-1">
                                <x-icon name="map-pin" class="size-3 text-zinc-400 flex-shrink-0" />
                                <span class="text-zinc-700 dark:text-zinc-300 truncate">{{ $vehicle['current_branch'] }}</span>
                            </div>
                            
                            @if($vehicle['speed'] > 0)
                                <div class="flex items-center gap-1">
                                    <x-icon name="gauge" class="size-3 text-zinc-400 flex-shrink-0" />
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ number_format($vehicle['speed'], 0) }} km/h</span>
                                </div>
                            @endif

                            @if($vehicle['type'] === 'conductor')
                                <div class="flex items-center gap-1">
                                    <x-icon name="package" class="size-3 text-zinc-400 flex-shrink-0" />
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ $vehicle['deliveries_completed'] }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <x-icon name="star" class="size-3 text-yellow-500 flex-shrink-0" />
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ number_format($vehicle['rating'], 1) }}</span>
                                </div>
                            @else
                                <div class="flex items-center gap-1">
                                    <x-icon name="navigation" class="size-3 text-zinc-400 flex-shrink-0" />
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ number_format($vehicle['odometer'], 0) }} km</span>
                                </div>
                            @endif
                        </div>

                        {{-- Alerta --}}
                        @if($vehicle['needs_maintenance'])
                            <div class="mt-2 p-1.5 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded flex items-center gap-1.5">
                                <x-icon name="alert-triangle" class="size-3 text-red-600 flex-shrink-0" />
                                <span class="text-xs text-red-700 dark:text-red-300 font-medium">Requiere mantenimiento</span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <x-icon name="inbox" class="size-10 text-zinc-400 mb-2" />
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">No hay dispositivos</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@assets
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
.leaflet-popup-content-wrapper {
    border-radius: 8px;
    padding: 0;
}
.leaflet-popup-content {
    margin: 0;
}
</style>
@endassets

@script
<script>
(function() {
    // Estado del mapa
    const mapState = {
        instance: null,
        markers: {},
        branches: {},
        branchCircles: {},      // ✅ Agregado para círculos de sedes
        branchMarkers: {},      // ✅ Agregado para marcadores de sedes
        routes: {},             // ✅ NUEVO: Almacenar rutas dibujadas
        routeMarkers: {},       // ✅ NUEVO: Marcadores de origen/destino
        currentRouteId: null,              // ✅ ID de la ruta actualmente mostrada
        currentRouteReservationId: null,   // ✅ Reserva asociada a la ruta actual
        autoRefresh: null,
        isReady: false,
        broadcastingEnabled: true,
        echoChannel: null,
        currentFilters: {
            type: 'all',
            status: 'all'
        }
    };

    // Base URL para ver detalle de reserva en panel admin
    const reservationDetailBaseUrl = @json(route('admin.reservations.index'));

    function getStatusColor(status) {
        const colors = {
            'active': '#3b82f6',
            'idle': '#6b7280',
            'charging': '#eab308',
            'maintenance': '#ef4444'
        };
        return colors[status] || '#6b7280';
    }

    function getBatteryColor(battery) {
        if (battery >= 70) return '#22c55e';
        if (battery >= 40) return '#eab308';
        return '#ef4444';
    }

    function createVehicleIcon(vehicle) {
        const color = getStatusColor(vehicle.status);
        const isMoving = vehicle.speed > 1;
        const isSimulated = !!vehicle.is_simulated;
        
        const iconType = vehicle.type === 'conductor' ? 'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z' : 
                                                          'M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z';
        
        return L.divIcon({
            html: `<div style="position:relative;width:32px;height:32px;">
                     <svg width="32" height="32" viewBox="0 0 32 32">
                       <circle cx="16" cy="16" r="14" fill="${color}" stroke="white" stroke-width="2" 
                               style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3))"/>
                     </svg>
                     <svg style="position:absolute;top:8px;left:8px;" width="16" height="16" viewBox="0 0 24 24" fill="white">
                       <path d="${iconType}"/>
                     </svg>
                     ${isSimulated ? `<div style="position:absolute;top:-10px;right:-10px;padding:2px 4px;border-radius:4px;background:#7c3aed;color:white;font-size:9px;font-weight:bold;box-shadow:0 1px 3px rgba(0,0,0,0.4);">SIM</div>` : ''}
                     ${isMoving ? `<div style="position:absolute;bottom:-16px;left:50%;transform:translateX(-50%);
                                   background:${color};color:white;padding:2px 6px;border-radius:4px;
                                   font-size:9px;font-weight:bold;white-space:nowrap;
                                   box-shadow:0 2px 4px rgba(0,0,0,0.3);border:1px solid white;">
                         ${Math.round(vehicle.speed)} km/h
                       </div>` : ''}
                   </div>`,
            className: '',
            iconSize: [32, 32],
            iconAnchor: [16, 16],
            popupAnchor: [0, -16]
        });
    }

    function createPopupContent(v) {
        const statusColor = getStatusColor(v.status);
        const batteryColor = getBatteryColor(v.battery);
        const healthColor = v.battery_health >= 80 ? '#22c55e' : v.battery_health >= 60 ? '#eab308' : '#ef4444';
        
        return `
            <div style="min-width:240px;font-family:system-ui;padding:12px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;padding-bottom:10px;border-bottom:2px solid ${statusColor};">
                    <div style="width:36px;height:36px;background:${statusColor};border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                            <path d="${v.type === 'conductor' 
                                ? 'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z' 
                                : 'M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z'}"/>
                        </svg>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:16px;font-weight:700;color:#18181b;">${v.device_id}</div>
                        ${v.type === 'conductor'
                            ? (v.driver_name ? `<div style="font-size:12px;color:#71717a;">${v.driver_name}</div>` : '')
                            : (v.driver_name ? `<div style="font-size:12px;color:#71717a;">Conductor: ${v.driver_name}</div>` : '')
                        }
                    </div>
                </div>
                
                <div style="font-size:13px;line-height:1.6;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin:8px 0;">
                        <span style="color:#71717a;">Estado:</span>
                        <span style="padding:4px 8px;background:${statusColor};color:white;border-radius:6px;font-size:11px;font-weight:600;">
                            ${v.status_label}
                        </span>
                    </div>
                    
                    <div style="margin:10px 0;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                            <span style="color:#71717a;">Batería</span>
                            <span style="font-weight:600;">${v.battery.toFixed(1)}%</span>
                        </div>
                        <div style="background:#e4e4e7;border-radius:999px;height:6px;overflow:hidden;">
                            <div style="background:${batteryColor};height:100%;width:${v.battery}%;border-radius:999px;transition:width 0.3s;"></div>
                        </div>
                    </div>
                    
                    <div style="margin:10px 0;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                            <span style="color:#71717a;">Salud</span>
                            <span style="font-weight:600;">${v.battery_health.toFixed(1)}%</span>
                        </div>
                        <div style="background:#e4e4e7;border-radius:999px;height:6px;overflow:hidden;">
                            <div style="background:${healthColor};height:100%;width:${v.battery_health}%;border-radius:999px;transition:width 0.3s;"></div>
                        </div>
                    </div>
                    
                    ${v.speed > 0 ? `
                    <div style="display:flex;justify-content:space-between;margin:8px 0;">
                        <span style="color:#71717a;">Velocidad</span>
                        <span style="font-weight:600;">${v.speed.toFixed(1)} km/h</span>
                    </div>
                    ` : ''}
                    
                    <div style="margin-top:10px;padding-top:10px;border-top:1px solid #e4e4e7;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:12px;">
                            <div>
                                <div style="color:#a1a1aa;font-size:10px;text-transform:uppercase;">Ubicación</div>
                                <div style="font-weight:600;color:#18181b;">${v.current_branch}</div>
                            </div>
                            ${v.target_branch ? `
                            <div>
                                <div style="color:#a1a1aa;font-size:10px;text-transform:uppercase;">Destino</div>
                                <div style="font-weight:600;color:#18181b;">${v.target_branch}</div>
                            </div>
                            ` : ''}
                        </div>
                        
                        ${v.type === 'conductor' ? `
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px;font-size:12px;">
                            <div>
                                <div style="color:#a1a1aa;font-size:10px;text-transform:uppercase;">Entregas</div>
                                <div style="font-weight:600;color:#18181b;">${v.deliveries_completed}</div>
                            </div>
                            <div>
                                <div style="color:#a1a1aa;font-size:10px;text-transform:uppercase;">Rating</div>
                                <div style="font-weight:600;color:#18181b;">${v.rating.toFixed(1)} ⭐</div>
                            </div>
                        </div>
                        ` : `
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px;font-size:12px;">
                            <div>
                                <div style="color:#a1a1aa;font-size:10px;text-transform:uppercase;">Odómetro</div>
                                <div style="font-weight:600;color:#18181b;">${v.odometer.toFixed(1)} km</div>
                            </div>
                            <div>
                                <div style="color:#a1a1aa;font-size:10px;text-transform:uppercase;">Viajes</div>
                                <div style="font-weight:600;color:#18181b;">${(v.trip_count ?? 0)}</div>
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr;gap:6px;margin-top:8px;font-size:12px;">
                            <div>
                                <div style="color:#a1a1aa;font-size:10px;text-transform:uppercase;">Mantenimiento</div>
                                <div style="font-weight:600;color:${v.maintenance_km_left <= 100 ? '#ef4444' : '#18181b'};">${v.maintenance_km_left.toFixed(0)} km</div>
                            </div>
                        </div>
                        ${v.active_reservation_id && v.type === 'vehiculo' ? `
                        <div style="margin-top:10px;display:flex;justify-content:flex-end;">
                            <a href="${reservationDetailBaseUrl.replace(/\/$/, '')}/${v.active_reservation_id}"
                               target="_blank"
                               style="display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;
                                      background:#0f172a;color:white;font-size:11px;font-weight:600;text-decoration:none;">
                                <span>Ver detalle</span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 17L17 7" />
                                    <path d="M7 7h10v10" />
                                </svg>
                            </a>
                        </div>
                        ` : ''}
                        `}
                    </div>
                    
                    ${v.needs_maintenance ? `
                    <div style="margin-top:10px;padding:8px;background:#fef2f2;border:1px solid #fecaca;
                                border-radius:6px;color:#dc2626;font-size:12px;font-weight:600;text-align:center;">
                        ⚠️ Requiere mantenimiento
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    // ========================================
    // 🗺️ FUNCIONES PARA DIBUJAR RUTAS OSRM
    // ========================================

    /**
     * Dibuja una ruta en el mapa usando datos de OSRM
     * @param {Object} routeData - Datos de la ruta { geometry, distance_km, duration_minutes }
     * @param {String} routeId - ID único para la ruta
     * @param {Object} options - Opciones de estilo { color, weight, opacity }
     */
    function drawRoute(routeData, routeId = 'default', options = {}) {
        if (!mapState.isReady || !mapState.instance) {
            console.warn('⚠️ Mapa no está listo para dibujar rutas');
            return;
        }

        // Remover ruta anterior si existe
        if (mapState.routes[routeId]) {
            mapState.instance.removeLayer(mapState.routes[routeId]);
            delete mapState.routes[routeId];
        }

        // Opciones de estilo por defecto
        const defaultOptions = {
            color: '#3b82f6',
            weight: 5,
            opacity: 0.8,
            lineJoin: 'round',
            lineCap: 'round',
            dashArray: null
        };

        const style = { ...defaultOptions, ...options };

        try {
            // Dibujar la ruta usando GeoJSON
            const routeLayer = L.geoJSON(routeData.geometry, {
                style: style
            }).addTo(mapState.instance);

            // Agregar popup con información de la ruta
            const popupContent = `
                <div style="padding:10px;font-family:system-ui;">
                    <div style="font-weight:700;font-size:14px;margin-bottom:8px;color:#18181b;">
                        📍 Información de Ruta
                    </div>
                    <div style="font-size:13px;line-height:1.8;">
                        <div style="display:flex;justify-content:space-between;margin:4px 0;">
                            <span style="color:#71717a;">Distancia:</span>
                            <span style="font-weight:600;color:#18181b;">${routeData.distance_km} km</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;margin:4px 0;">
                            <span style="color:#71717a;">Duración:</span>
                            <span style="font-weight:600;color:#18181b;">${routeData.duration_minutes} min</span>
                        </div>
                        ${routeData.price ? `
                        <div style="display:flex;justify-content:space-between;margin:4px 0;padding-top:6px;border-top:1px solid #e4e4e7;">
                            <span style="color:#71717a;">Precio:</span>
                            <span style="font-weight:700;color:#3b82f6;font-size:15px;">$${routeData.price}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;

            routeLayer.bindPopup(popupContent, { maxWidth: 250 });

            // Guardar referencia
            mapState.routes[routeId] = routeLayer;

            // Ajustar vista del mapa para mostrar toda la ruta
            mapState.instance.fitBounds(routeLayer.getBounds(), {
                padding: [50, 50],
                maxZoom: 15
            });

            console.log(`✅ Ruta "${routeId}" dibujada exitosamente`);
            return routeLayer;

        } catch (error) {
            console.error('❌ Error dibujando ruta:', error);
            return null;
        }
    }

    /**
     * Agrega marcadores de origen y destino a una ruta
     * @param {Number} originLat - Latitud del origen
     * @param {Number} originLng - Longitud del origen
     * @param {Number} destLat - Latitud del destino
     * @param {Number} destLng - Longitud del destino
     * @param {String} routeId - ID de la ruta asociada
     */
    function addRouteMarkers(originLat, originLng, destLat, destLng, routeId = 'default') {
        if (!mapState.isReady || !mapState.instance) return;

        // Remover marcadores anteriores si existen
        if (mapState.routeMarkers[routeId]) {
            mapState.routeMarkers[routeId].forEach(marker => {
                mapState.instance.removeLayer(marker);
            });
        }

        // Crear icono de origen (verde)
        const originIcon = L.divIcon({
            html: `<div style="position:relative;">
                     <svg width="32" height="40" viewBox="0 0 32 40">
                       <path d="M16 0C7.2 0 0 7.2 0 16c0 8.8 16 24 16 24s16-15.2 16-24C32 7.2 24.8 0 16 0z" 
                             fill="#22c55e" stroke="white" stroke-width="2"/>
                       <circle cx="16" cy="16" r="6" fill="white"/>
                       <text x="16" y="20" text-anchor="middle" font-size="12" font-weight="bold" fill="#22c55e">A</text>
                     </svg>
                   </div>`,
            className: '',
            iconSize: [32, 40],
            iconAnchor: [16, 40],
            popupAnchor: [0, -40]
        });

        // Crear icono de destino (rojo)
        const destIcon = L.divIcon({
            html: `<div style="position:relative;">
                     <svg width="32" height="40" viewBox="0 0 32 40">
                       <path d="M16 0C7.2 0 0 7.2 0 16c0 8.8 16 24 16 24s16-15.2 16-24C32 7.2 24.8 0 16 0z" 
                             fill="#ef4444" stroke="white" stroke-width="2"/>
                       <circle cx="16" cy="16" r="6" fill="white"/>
                       <text x="16" y="20" text-anchor="middle" font-size="12" font-weight="bold" fill="#ef4444">B</text>
                     </svg>
                   </div>`,
            className: '',
            iconSize: [32, 40],
            iconAnchor: [16, 40],
            popupAnchor: [0, -40]
        });

        // Agregar marcador de origen
        const originMarker = L.marker([originLat, originLng], { 
            icon: originIcon,
            zIndexOffset: 1000
        }).addTo(mapState.instance);
        
        originMarker.bindPopup(`
            <div style="padding:8px;font-family:system-ui;">
                <div style="font-weight:700;color:#22c55e;margin-bottom:4px;">🟢 Origen</div>
                <div style="font-size:12px;color:#71717a;">
                    ${originLat.toFixed(6)}, ${originLng.toFixed(6)}
                </div>
            </div>
        `);

        // Agregar marcador de destino
        const destMarker = L.marker([destLat, destLng], { 
            icon: destIcon,
            zIndexOffset: 1000
        }).addTo(mapState.instance);
        
        destMarker.bindPopup(`
            <div style="padding:8px;font-family:system-ui;">
                <div style="font-weight:700;color:#ef4444;margin-bottom:4px;">🔴 Destino</div>
                <div style="font-size:12px;color:#71717a;">
                    ${destLat.toFixed(6)}, ${destLng.toFixed(6)}
                </div>
            </div>
        `);

        // Guardar referencias
        mapState.routeMarkers[routeId] = [originMarker, destMarker];
    }

    /**
     * Calcula y dibuja una ruta usando el backend Laravel + OSRM
     * @param {Number} originLat - Latitud del origen
     * @param {Number} originLng - Longitud del origen
     * @param {Number} destLat - Latitud del destino
     * @param {Number} destLng - Longitud del destino
     * @param {String} routeId - ID único para la ruta
     */
    async function calculateAndDrawRoute(originLat, originLng, destLat, destLng, routeId = 'default') {
        if (!mapState.isReady || !mapState.instance) {
            console.warn('⚠️ Mapa no está listo');
            return;
        }

        try {
            console.log('🔄 Calculando ruta...');

            // Llamar al backend Laravel (API REST). Requiere auth válida (Sanctum/token) configurada a nivel de proyecto.
            const response = await fetch('/api/routes/calculate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    origin_lat: originLat,
                    origin_lng: originLng,
                    dest_lat: destLat,
                    dest_lng: destLng
                })
            });

            const result = await response.json();

            if (result.success && result.data) {
                // Dibujar la ruta
                drawRoute(result.data, routeId);
                
                // Agregar marcadores de origen/destino
                addRouteMarkers(originLat, originLng, destLat, destLng, routeId);

                // Mostrar notificación de éxito
                showNotification(
                    `✅ Ruta calculada: ${result.data.distance_km} km, ${result.data.duration_minutes} min`,
                    'success'
                );

                return result.data;
            } else {
                throw new Error(result.message || 'Error al calcular ruta');
            }

        } catch (error) {
            console.error('❌ Error calculando ruta:', error);
            showNotification('❌ Error al calcular la ruta: ' + error.message, 'error');
            return null;
        }
    }

    /**
     * Limpia una ruta específica del mapa
     * @param {String} routeId - ID de la ruta a limpiar
     */
    function clearRoute(routeId = 'default') {
        // Remover línea de ruta
        if (mapState.routes[routeId]) {
            mapState.instance.removeLayer(mapState.routes[routeId]);
            delete mapState.routes[routeId];
        }

        // Remover marcadores
        if (mapState.routeMarkers[routeId]) {
            mapState.routeMarkers[routeId].forEach(marker => {
                mapState.instance.removeLayer(marker);
            });
            delete mapState.routeMarkers[routeId];
        }

        if (mapState.currentRouteId === routeId) {
            mapState.currentRouteId = null;
            mapState.currentRouteReservationId = null;
        }

        console.log(`🗑️ Ruta "${routeId}" limpiada`);
    }

    /**
     * Limpia todas las rutas del mapa
     */
    function clearAllRoutes() {
        Object.keys(mapState.routes).forEach(routeId => {
            clearRoute(routeId);
        });
        mapState.currentRouteId = null;
        mapState.currentRouteReservationId = null;
        console.log('🗑️ Todas las rutas limpiadas');
    }

    /**
     * Muestra una notificación temporal
     */
    function showNotification(message, type = 'info') {
        // Si existe Livewire, usar su sistema de notificaciones
        if (typeof $wire !== 'undefined') {
            $wire.dispatch('showNotification', { message, type });
        } else {
            // Fallback: console
            console.log(`[${type.toUpperCase()}] ${message}`);
        }
    }


    function loadBranches(branches) {
        if (!mapState.isReady || !mapState.instance) return;
        
        branches.forEach(branch => {
            const branchId = `branch-${branch.id}`;

            if (!mapState.branchCircles[branchId]) {
                const circle = L.circle([branch.lat, branch.lng], {
                    color: branch.color,
                    fillColor: branch.color,
                    fillOpacity: 0.1,
                    radius: branch.radio,
                    weight: 2,
                    dashArray: '5, 10',
                    opacity: 0.6
                }).addTo(mapState.instance);

                const ocupacionColor = branch.ocupacion_porcentaje > 80 ? '#ef4444' : 
                                      branch.ocupacion_porcentaje > 50 ? '#eab308' : '#22c55e';

                circle.bindPopup(`
                    <div style="min-width:220px;font-family:system-ui;padding:12px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;padding-bottom:10px;border-bottom:2px solid ${branch.color};">
                            <div style="width:36px;height:36px;background:${branch.color};border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;">
                                🏢
                            </div>
                            <div>
                                <div style="font-size:16px;font-weight:700;color:${branch.color};">${branch.nombre}</div>
                                <div style="font-size:11px;color:#71717a;">${branch.descripcion || 'Sede'}</div>
                            </div>
                        </div>
                        <div style="font-size:13px;line-height:1.6;">
                            <div style="display:flex;justify-content:space-between;margin:6px 0;">
                                <span style="color:#71717a;">Radio:</span>
                                <span style="font-weight:600;">${branch.radio}m</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;margin:6px 0;">
                                <span style="color:#71717a;">Capacidad:</span>
                                <span style="font-weight:600;">${branch.dispositivos_actuales} / ${branch.capacidad}</span>
                            </div>
                            <div style="margin:10px 0;">
                                <div style="font-size:11px;color:#71717a;margin-bottom:4px;">Ocupación</div>
                                <div style="background:#e4e4e7;border-radius:999px;height:6px;overflow:hidden;">
                                    <div style="background:${ocupacionColor};height:100%;width:${branch.ocupacion_porcentaje}%;
                                                border-radius:999px;transition:width 0.3s;"></div>
                                </div>
                                <div style="font-size:11px;color:#71717a;margin-top:4px;text-align:right;">
                                    ${branch.ocupacion_porcentaje}%
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                mapState.branchCircles[branchId] = circle;

                const marker = L.marker([branch.lat, branch.lng], {
                    icon: L.divIcon({
                        html: `<div style="background:${branch.color};color:white;padding:4px 10px;border-radius:8px;
                                          font-size:11px;font-weight:700;white-space:nowrap;
                                          box-shadow:0 2px 8px rgba(0,0,0,0.2);border:2px solid white;">
                                 ${branch.nombre} (${branch.dispositivos_actuales})
                               </div>`,
                        className: '',
                        iconSize: [null, null],
                        iconAnchor: [0, -8]
                    }),
                    zIndexOffset: 1000
                }).addTo(mapState.instance);

                mapState.branchMarkers[branchId] = marker;
            }
        });
    }

    function loadVehicles(vehicles) {
        if (!mapState.isReady || !mapState.instance) {
            setTimeout(() => {
                if (initMap()) loadVehicles(vehicles);
            }, 500);
            return;
        }

        if (!Array.isArray(vehicles)) return;

        const currentIds = vehicles.map(v => v.device_id);
        
        // Eliminar marcadores que ya no existen
        Object.keys(mapState.markers).forEach(id => {
            if (!currentIds.includes(id)) {
                mapState.instance.removeLayer(mapState.markers[id]);
                delete mapState.markers[id];
            }
        });

        // Actualizar o crear marcadores (aplicando filtros)
        vehicles.forEach(v => {
            if (!v.lat || !v.lng) return;

            // Aplicar filtros
            if (!shouldShowVehicle(v)) {
                // Si no pasa los filtros, remover si existe
                if (mapState.markers[v.device_id]) {
                    mapState.instance.removeLayer(mapState.markers[v.device_id]);
                    delete mapState.markers[v.device_id];
                }
                return;
            }

            const icon = createVehicleIcon(v);
            const popup = createPopupContent(v);

            if (!mapState.markers[v.device_id]) {
                // Crear nuevo marcador
                const marker = L.marker([v.lat, v.lng], { icon }).addTo(mapState.instance);
                marker.bindPopup(popup, { maxWidth: 280 });
                mapState.markers[v.device_id] = marker;
            } else {
                // Actualizar marcador existente
                const marker = mapState.markers[v.device_id];
                const oldLatLng = marker.getLatLng();
                const newLatLng = L.latLng(v.lat, v.lng);
                const distance = oldLatLng.distanceTo(newLatLng);
                
                // Solo actualizar si hay cambio significativo
                if (distance > 0.5) {
                    marker.setLatLng(newLatLng);
                }
                
                marker.setIcon(icon);
                marker.getPopup().setContent(popup);
            }
        });
    }

    function initMap() {
        if (mapState.isReady) return true;

        const container = document.getElementById('map');
        if (!container) return false;

        try {
            if (mapState.instance) mapState.instance.remove();

            mapState.instance = L.map('map', {
                zoomControl: true,
                attributionControl: true
            }).setView([7.1193, -73.1227], 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(mapState.instance);

            setTimeout(() => {
                if (mapState.instance) mapState.instance.invalidateSize();
            }, 200);

            mapState.isReady = true;
            return true;
        } catch (err) {
            console.error('❌ Error inicializando mapa:', err);
            return false;
        }
    }

    function startAutoRefresh(seconds) {
        if (mapState.autoRefresh) clearInterval(mapState.autoRefresh);
        mapState.autoRefresh = setInterval(() => {
            $wire.call('loadMapData');
        }, seconds * 1000);
    }

    function stopAutoRefresh() {
        if (mapState.autoRefresh) {
            clearInterval(mapState.autoRefresh);
            mapState.autoRefresh = null;
        }
    }

    // Eventos de Livewire
    Livewire.on('mapDataUpdated', (event) => {
        const data = Array.isArray(event) ? event[0] : event;
        
        if (data.branches && data.branches.length > 0) {
            loadBranches(data.branches);
        }
        
        if (data.vehicles && data.vehicles.length > 0) {
            loadVehicles(data.vehicles);
        }
    });

    Livewire.on('focusOnVehicle', (event) => {
        const data = Array.isArray(event) ? event[0] : event;
        const vehicle = data.vehicle || data;
        
        if (vehicle && mapState.markers[vehicle.device_id]) {
            // Al cambiar de vehículo, limpiar cualquier ruta dibujada previamente
            clearAllRoutes();

            mapState.instance.setView([vehicle.lat, vehicle.lng], 16, { 
                animate: true,
                duration: 0.8
            });
            
            setTimeout(() => {
                mapState.markers[vehicle.device_id].openPopup();
            }, 600);
        }
    });

    Livewire.on('startAutoRefresh', (event) => {
        const data = Array.isArray(event) ? event[0] : event;
        startAutoRefresh(data.interval || 3);
    });

    Livewire.on('stopAutoRefresh', () => {
        stopAutoRefresh();
        stopBroadcasting();
    });

    Livewire.on('startAutoRefresh', (event) => {
        const data = Array.isArray(event) ? event[0] : event;
        startAutoRefresh(data.interval || 3);
        startBroadcasting();
    });

    // ========================================
    // 🗺️ EVENTOS DE LIVEWIRE PARA RUTAS
    // ========================================

    /**
     * Evento: Dibujar una ruta desde el backend
     * Uso: $this->dispatch('drawRoute', route: $routeData, routeId: 'my-route')
     */
    Livewire.on('drawRoute', (event) => {
        const data = Array.isArray(event) ? event[0] : event;
        
        if (data.route) {
            const routeId = data.routeId || data.route_id || 'default';
            const options = data.options || {};
            
            drawRoute(data.route, routeId, options);
            
            // Si hay coordenadas de origen/destino, agregar marcadores
            if (data.origin_lat && data.origin_lng && data.dest_lat && data.dest_lng) {
                addRouteMarkers(
                    data.origin_lat, 
                    data.origin_lng, 
                    data.dest_lat, 
                    data.dest_lng, 
                    routeId
                );
            }
        }
    });

    /**
     * Evento: Calcular y dibujar ruta
     * Uso: $this->dispatch('calculateRoute', originLat: 7.1, originLng: -73.1, ...)
     */
    Livewire.on('calculateRoute', (event) => {
        const data = Array.isArray(event) ? event[0] : event;
        
        if (data.origin_lat && data.origin_lng && data.dest_lat && data.dest_lng) {
            const routeId = data.routeId || data.route_id || 'default';
            
            calculateAndDrawRoute(
                data.origin_lat,
                data.origin_lng,
                data.dest_lat,
                data.dest_lng,
                routeId
            );
        }
    });

    /**
     * Evento: Limpiar una ruta específica
     * Uso: $this->dispatch('clearRoute', routeId: 'my-route')
     */
    Livewire.on('clearRoute', (event) => {
        const data = Array.isArray(event) ? event[0] : event;
        const routeId = data.routeId || data.route_id || 'default';
        
        clearRoute(routeId);
    });

    /**
     * Evento: Limpiar todas las rutas
     * Uso: $this->dispatch('clearAllRoutes')
     */
    Livewire.on('clearAllRoutes', () => {
        clearAllRoutes();
    });

    /**
     * Evento: Mostrar ruta de reserva
     * Uso: $this->dispatch('showReservationRoute', reservation: $reservation)
     */
    Livewire.on('showReservationRoute', (event) => {
        const data = Array.isArray(event) ? event[0] : event;
        const reservation = data.reservation || data;
        
        // Asegurar que el mapa esté inicializado
        if (!mapState.isReady || !mapState.instance) {
            const ok = typeof initMap === 'function' ? initMap() : false;
            if (!ok || !mapState.instance) {
                console.warn('⚠️ No se pudo inicializar el mapa para mostrar la reserva');
                return;
            }
        }

        // Antes de dibujar una nueva ruta de reserva, limpiar todas las anteriores
        clearAllRoutes();

        if (reservation.waypoints) {
            const routeData = {
                geometry: reservation.waypoints,
                distance_km: reservation.distancia_km,
                duration_minutes: reservation.duracion_minutos,
                price: reservation.monto_final
            };
            
            const routeId = `reservation-${reservation.id}`;
            mapState.currentRouteId = routeId;
            mapState.currentRouteReservationId = reservation.id;
            
            // Dibujar ruta con estilo especial para reservas
            const options = {
                color: reservation.estado === 'activa' ? '#10b981' : '#6b7280',
                weight: 4,
                opacity: 0.7,
                dashArray: reservation.estado === 'pendiente' ? '10, 5' : null
            };
            
            drawRoute(routeData, routeId, options);
            
            // Agregar marcadores
            if (reservation.origen_lat && reservation.destino_lat) {
                addRouteMarkers(
                    reservation.origen_lat,
                    reservation.origen_lng,
                    reservation.destino_lat,
                    reservation.destino_lng,
                    routeId
                );
            }
        } else if (reservation.branch_lat && reservation.branch_lng) {
            // Sin geometría: centrar en la sede asociada a la reserva
            mapState.instance.setView([
                reservation.branch_lat,
                reservation.branch_lng
            ], 14, {
                animate: true,
                duration: 0.8
            });
        }
    });

    // Función para verificar si un vehículo pasa los filtros
    function shouldShowVehicle(vehicle) {
        const filters = mapState.currentFilters;
        
        // Filtro por tipo
        if (filters.type !== 'all' && vehicle.type !== filters.type) {
            return false;
        }
        
        // Filtro por estado
        if (filters.status !== 'all' && vehicle.status !== filters.status) {
            return false;
        }
        
        return true;
    }

    // Escuchar cambios en filtros desde Livewire
    Livewire.hook('morph.updated', ({ el, component }) => {
        // Actualizar filtros locales cuando cambian en Livewire
        const typeSelect = document.querySelector('[wire\\:model\\.live="filterType"]');
        const statusSelect = document.querySelector('[wire\\:model\\.live="filterStatus"]');
        
        if (typeSelect) {
            mapState.currentFilters.type = typeSelect.value;
        }
        if (statusSelect) {
            mapState.currentFilters.status = statusSelect.value;
        }
    });

    // Laravel Echo - Broadcasting en tiempo real
    function setupBroadcasting() {
        if (typeof window.Echo === 'undefined') {
            console.warn('⚠️ Laravel Echo no disponible, usando polling como fallback');
            return;
        }

        // Guardar referencia al canal
        mapState.echoChannel = window.Echo.channel('telemetry')
            .listen('.telemetry.updated', (event) => {
                // Solo actualizar si broadcasting está habilitado
                if (mapState.broadcastingEnabled) {
                    updateSingleVehicle(event);
                }
            });
    }

    function stopBroadcasting() {
        if (mapState.echoChannel) {
            window.Echo.leave('telemetry');
            mapState.echoChannel = null;
        }
        mapState.broadcastingEnabled = false;
    }

    function startBroadcasting() {
        mapState.broadcastingEnabled = true;
        if (!mapState.echoChannel) {
            setupBroadcasting();
        }
    }

    function updateSingleVehicle(telemetry) {
        if (!mapState.isReady || !mapState.instance) return;

        const vehicle = {
            device_id: telemetry.device_id,
            type: telemetry.type,
            status: telemetry.status,
            status_label: getStatusLabel(telemetry.status),
            lat: telemetry.lat,
            lng: telemetry.lng,
            battery: telemetry.battery,
            battery_health: telemetry.battery_health,
            speed: telemetry.speed,
            current_branch: telemetry.current_branch,
            target_branch: telemetry.target_branch,
            odometer: telemetry.odometer,
            trip_count: telemetry.trip_count,
            maintenance_km_left: telemetry.maintenance_km_left,
            needs_maintenance: telemetry.needs_maintenance,
            driver_name: telemetry.driver_name,
            deliveries_completed: telemetry.deliveries_completed,
            rating: telemetry.rating
        };

        // Aplicar filtros
        if (!shouldShowVehicle(vehicle)) {
            // Si el vehículo no pasa los filtros, removerlo si existe
            if (mapState.markers[vehicle.device_id]) {
                mapState.instance.removeLayer(mapState.markers[vehicle.device_id]);
                delete mapState.markers[vehicle.device_id];
            }
            return;
        }

        const icon = createVehicleIcon(vehicle);
        const popup = createPopupContent(vehicle);

        if (!mapState.markers[vehicle.device_id]) {
            // Crear nuevo marcador
            const marker = L.marker([vehicle.lat, vehicle.lng], { icon }).addTo(mapState.instance);
            marker.bindPopup(popup, { maxWidth: 280 });
            mapState.markers[vehicle.device_id] = marker;
        } else {
            // Actualizar marcador existente con animación suave
            const marker = mapState.markers[vehicle.device_id];
            const oldLatLng = marker.getLatLng();
            const newLatLng = L.latLng(vehicle.lat, vehicle.lng);
            
            marker.setLatLng(newLatLng);
            marker.setIcon(icon);
            marker.getPopup().setContent(popup);
        }

        // Actualizar estadísticas en Livewire cada 10 actualizaciones
        if (Math.random() < 0.1) {
            $wire.call('loadMapData');
        }
    }

    function getStatusLabel(status) {
        const labels = {
            'active': 'En ruta',
            'idle': 'En espera',
            'charging': 'Cargando',
            'maintenance': 'Mantenimiento',
            'offline': 'Desconectado'
        };
        return labels[status] || 'Desconocido';
    }

    // Inicialización
    setTimeout(() => {
        if (initMap()) {
            const vehicles = {!! json_encode($vehicles) !!};
            const branches = {!! json_encode($branches) !!};
            
            if (branches && branches.length > 0) {
                setTimeout(() => loadBranches(branches), 300);
            }
            
            if (vehicles && vehicles.length > 0) {
                setTimeout(() => loadVehicles(vehicles), 600);
            }

            // Configurar broadcasting en tiempo real
            setTimeout(() => setupBroadcasting(), 1000);

            @if($autoRefresh)
                setTimeout(() => startAutoRefresh({{ $refreshInterval }}), 1500);
            @endif
        }
    }, 100);

    // ========================================
    // 🌐 EXPONER FUNCIONES GLOBALMENTE
    // ========================================
    // Esto permite usar las funciones desde la consola del navegador
    
    window.BgaGOMap = {
        // Estado del mapa
        state: mapState,
        
        // Funciones de rutas
        drawRoute: drawRoute,
        addRouteMarkers: addRouteMarkers,
        calculateAndDrawRoute: calculateAndDrawRoute,
        clearRoute: clearRoute,
        clearAllRoutes: clearAllRoutes,
        
        // Funciones de vehículos
        loadVehicles: loadVehicles,
        loadBranches: loadBranches,
        
        // Utilidades
        getStatusColor: getStatusColor,
        getBatteryColor: getBatteryColor,
        
        // Acceso directo al mapa de Leaflet
        getMap: () => mapState.instance
    };

    // Log de bienvenida
    console.log('%c🗺️ BgaGO Map API Ready!', 'color: #3b82f6; font-size: 16px; font-weight: bold;');
    console.log('%cPrueba estas funciones en la consola:', 'color: #6b7280; font-size: 12px;');
    console.log('%c  BgaGOMap.drawRoute(routeData, "my-route")', 'color: #10b981; font-size: 11px;');
    console.log('%c  BgaGOMap.calculateAndDrawRoute(7.1193, -73.1227, 7.0652, -73.0889)', 'color: #10b981; font-size: 11px;');
    console.log('%c  BgaGOMap.clearAllRoutes()', 'color: #10b981; font-size: 11px;');
    console.log('%cVer guía completa: .windsurf/docs/GUIA_DIBUJAR_RUTAS.md', 'color: #6b7280; font-size: 11px;');
})();
</script>
@endscript