<div class="flex flex-col gap-4 h-full">
    {{-- Panel de Estadísticas Superior --}}
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl shadow-sm border border-blue-200 dark:border-blue-800 p-4 hover:shadow-md transition-all">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-blue-500 rounded-lg shadow-sm">
                    <x-icon name="radio" class="size-5 text-white" />
                </div>
                <div>
                    <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $stats['total_devices'] }}</div>
                    <div class="text-xs font-medium text-blue-700 dark:text-blue-300">Dispositivos</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl shadow-sm border border-green-200 dark:border-green-800 p-4 hover:shadow-md transition-all">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-green-500 rounded-lg shadow-sm">
                    <x-icon name="car" class="size-5 text-white" />
                </div>
                <div>
                    <div class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $stats['active_vehicles'] }}</div>
                    <div class="text-xs font-medium text-green-700 dark:text-green-300">Vehículos activos</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl shadow-sm border border-purple-200 dark:border-purple-800 p-4 hover:shadow-md transition-all">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-purple-500 rounded-lg shadow-sm">
                    <x-icon name="bike" class="size-5 text-white" />
                </div>
                <div>
                    <div class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $stats['active_conductors'] }}</div>
                    <div class="text-xs font-medium text-purple-700 dark:text-purple-300">Conductores activos</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 rounded-xl shadow-sm border border-yellow-200 dark:border-yellow-800 p-4 hover:shadow-md transition-all">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-yellow-500 rounded-lg shadow-sm">
                    <x-icon name="zap" class="size-5 text-white" />
                </div>
                <div>
                    <div class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ $stats['charging'] }}</div>
                    <div class="text-xs font-medium text-yellow-700 dark:text-yellow-300">Cargando</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-xl shadow-sm border border-red-200 dark:border-red-800 p-4 hover:shadow-md transition-all">
            <div class="flex items-center gap-3">
                <div class="p-2.5 {{ $stats['maintenance_needed'] > 0 ? 'bg-red-500' : 'bg-zinc-400' }} rounded-lg shadow-sm">
                    <x-icon name="wrench" class="size-5 text-white" />
                </div>
                <div>
                    <div class="text-2xl font-bold {{ $stats['maintenance_needed'] > 0 ? 'text-red-900 dark:text-red-100' : 'text-zinc-700 dark:text-zinc-300' }}">
                        {{ $stats['maintenance_needed'] }}
                    </div>
                    <div class="text-xs font-medium {{ $stats['maintenance_needed'] > 0 ? 'text-red-700 dark:text-red-300' : 'text-zinc-600 dark:text-zinc-400' }}">Mantenimiento</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20 rounded-xl shadow-sm border border-cyan-200 dark:border-cyan-800 p-4 hover:shadow-md transition-all">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-cyan-500 rounded-lg shadow-sm">
                    <x-icon name="battery-charging" class="size-5 text-white" />
                </div>
                <div>
                    <div class="text-2xl font-bold text-cyan-900 dark:text-cyan-100">{{ $stats['avg_battery'] }}%</div>
                    <div class="text-xs font-medium text-cyan-700 dark:text-cyan-300">Batería promedio</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Barra de Controles --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <div class="flex flex-wrap items-center gap-3">
            <button wire:click="refreshMap" 
                    class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-lg transition-all text-sm font-semibold shadow-sm hover:shadow">
                <x-icon name="refresh-cw" class="size-4" />
                Actualizar
            </button>

            <button wire:click="toggleAutoRefresh" 
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all text-sm font-semibold shadow-sm hover:shadow
                           {{ $autoRefresh ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-700 dark:text-zinc-300' }}">
                <x-icon name="refresh-cw" class="size-4 {{ $autoRefresh ? 'animate-spin' : '' }}" />
                Auto {{ $autoRefresh ? 'ON' : 'OFF' }} ({{ $refreshInterval }}s)
            </button>
            
            <div class="h-6 w-px bg-zinc-300 dark:bg-zinc-600"></div>
            
            <select wire:model.live="filterType" class="px-4 py-2.5 rounded-lg border-2 border-zinc-200 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm font-medium hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 transition-all">
                <option value="all">Todos los tipos</option>
                <option value="vehiculo">Vehículos</option>
                <option value="conductor">Conductores</option>
            </select>
            
            <select wire:model.live="filterStatus" class="px-4 py-2.5 rounded-lg border-2 border-zinc-200 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm font-medium hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 transition-all">
                <option value="all">Todos los estados</option>
                <option value="active">En ruta</option>
                <option value="idle">En espera</option>
                <option value="charging">Cargando</option>
                <option value="maintenance">Mantenimiento</option>
            </select>

            <div class="flex-1"></div>

            <div class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">
                Última actualización: {{ now()->format('H:i:s') }}
            </div>
        </div>
    </div>

    {{-- Mapa y Panel Lateral --}}
    <div class="flex-1 flex gap-4 min-h-[600px]">
        {{-- Mapa Principal --}}
        <div class="flex-1 bg-white dark:bg-zinc-800 rounded-xl shadow-md border border-zinc-200 dark:border-zinc-700 relative overflow-hidden">
            <div id="map" wire:ignore style="width:100%;height:100%;min-height:600px;border-radius:0.75rem;"></div>
            
            {{-- Leyenda Mejorada --}}
            <div class="absolute top-4 left-4 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-sm rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 p-4 z-[1000] max-w-[250px]">
                <div class="text-sm font-bold mb-3 text-zinc-900 dark:text-white flex items-center gap-2">
                    <x-icon name="info" class="size-4" />
                    Leyenda
                </div>
                
                <div class="space-y-2.5 text-xs">
                    <div class="font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Estados:</div>
                    
                    <div class="flex items-center gap-2.5 pl-2">
                        <div class="w-4 h-4 rounded-full bg-blue-500 shadow-sm"></div>
                        <span class="text-zinc-600 dark:text-zinc-400">En ruta / Activo</span>
                    </div>
                    
                    <div class="flex items-center gap-2.5 pl-2">
                        <div class="w-4 h-4 rounded-full bg-gray-500 shadow-sm"></div>
                        <span class="text-zinc-600 dark:text-zinc-400">En espera</span>
                    </div>
                    
                    <div class="flex items-center gap-2.5 pl-2">
                        <div class="w-4 h-4 rounded-full bg-yellow-500 shadow-sm"></div>
                        <span class="text-zinc-600 dark:text-zinc-400">Cargando batería</span>
                    </div>
                    
                    <div class="flex items-center gap-2.5 pl-2">
                        <div class="w-4 h-4 rounded-full bg-red-500 shadow-sm"></div>
                        <span class="text-zinc-600 dark:text-zinc-400">Mantenimiento</span>
                    </div>

                    <div class="border-t border-zinc-200 dark:border-zinc-700 my-2"></div>
                    <div class="font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Tipos:</div>

                    <div class="flex items-center gap-2.5 pl-2">
                        <x-icon name="car" class="size-4 text-blue-600" />
                        <span class="text-zinc-600 dark:text-zinc-400">Vehículo</span>
                    </div>

                    <div class="flex items-center gap-2.5 pl-2">
                        <x-icon name="bike" class="size-4 text-purple-600" />
                        <span class="text-zinc-600 dark:text-zinc-400">Conductor</span>
                    </div>

                    <div class="border-t border-zinc-200 dark:border-zinc-700 my-2"></div>

                    <div class="flex items-center gap-2.5 pl-2">
                        <div class="w-4 h-4 rounded-full border-2 border-blue-500 bg-blue-100 dark:bg-blue-900/30"></div>
                        <span class="text-zinc-600 dark:text-zinc-400">Sede/Sucursal</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel Lateral con Lista de Dispositivos --}}
        <div class="w-96 bg-white dark:bg-zinc-800 rounded-xl shadow-md border border-zinc-200 dark:border-zinc-700 overflow-hidden flex flex-col">
            {{-- Header del Panel --}}
            <div class="p-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <x-icon name="map-pin" class="size-5" />
                    Dispositivos en Tiempo Real
                </h3>
                <p class="text-sm text-blue-100 mt-1">
                    {{ count($vehicles) }} dispositivo(s) activo(s)
                </p>
            </div>

            {{-- Lista de Dispositivos --}}
            <div class="flex-1 overflow-y-auto p-3 space-y-2.5 bg-zinc-50 dark:bg-zinc-900/50">
                @forelse($vehicles as $vehicle)
                    <div wire:click="focusVehicle('{{ $vehicle['device_id'] }}')" 
                         class="group p-3.5 rounded-xl cursor-pointer transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]
                                {{ $vehicle['status'] === 'active' ? 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20 border-2 border-blue-300 dark:border-blue-700 shadow-sm hover:shadow-md' : 
                                   ($vehicle['status'] === 'charging' ? 'bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/30 dark:to-yellow-800/20 border-2 border-yellow-300 dark:border-yellow-700 shadow-sm hover:shadow-md' : 
                                   ($vehicle['status'] === 'maintenance' ? 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/20 border-2 border-red-300 dark:border-red-700 shadow-sm hover:shadow-md' : 
                                   'bg-white dark:bg-zinc-800 border-2 border-zinc-200 dark:border-zinc-700 hover:border-blue-400 shadow-sm hover:shadow')) }}">
                        
                        {{-- Header de la tarjeta --}}
                        <div class="flex items-start justify-between mb-2.5">
                            <div class="flex items-center gap-2.5">
                                <div class="p-2 rounded-lg {{ $vehicle['type'] === 'conductor' ? 'bg-purple-500' : 'bg-blue-500' }} shadow-sm">
                                    <x-icon name="{{ $vehicle['type'] === 'conductor' ? 'bike' : 'car' }}" class="size-5 text-white" />
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-zinc-900 dark:text-white">{{ $vehicle['device_id'] }}</div>
                                    @if($vehicle['type'] === 'conductor')
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">{{ $vehicle['driver_name'] }}</div>
                                    @endif
                                </div>
                            </div>
                            
                            <span class="px-2 py-1 rounded-lg text-xs font-bold shadow-sm
                                        {{ $vehicle['status'] === 'active' ? 'bg-blue-500 text-white' : 
                                           ($vehicle['status'] === 'charging' ? 'bg-yellow-500 text-white' : 
                                           ($vehicle['status'] === 'maintenance' ? 'bg-red-500 text-white' : 
                                           'bg-zinc-400 text-white')) }}">
                                {{ $vehicle['status_label'] }}
                            </span>
                        </div>

                        {{-- Información principal --}}
                        <div class="space-y-2 mb-2.5">
                            {{-- Batería --}}
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-1.5">
                                    <x-icon name="battery-charging" class="size-3.5 text-zinc-500" />
                                    <span class="font-medium text-zinc-700 dark:text-zinc-300">Batería</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-20 h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500
                                                   {{ $vehicle['battery'] >= 70 ? 'bg-green-500' : 
                                                      ($vehicle['battery'] >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                             style="width: {{ $vehicle['battery'] }}%"></div>
                                    </div>
                                    <span class="font-bold text-zinc-900 dark:text-white min-w-[35px] text-right">{{ number_format($vehicle['battery'], 1) }}%</span>
                                </div>
                            </div>

                            {{-- Salud --}}
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-1.5">
                                    <x-icon name="heart-pulse" class="size-3.5 text-zinc-500" />
                                    <span class="font-medium text-zinc-700 dark:text-zinc-300">Salud</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-20 h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500
                                                   {{ $vehicle['battery_health'] >= 80 ? 'bg-green-500' : 
                                                      ($vehicle['battery_health'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                             style="width: {{ $vehicle['battery_health'] }}%"></div>
                                    </div>
                                    <span class="font-bold text-zinc-900 dark:text-white min-w-[35px] text-right">{{ number_format($vehicle['battery_health'], 1) }}%</span>
                                </div>
                            </div>
                        </div>

                        {{-- Detalles adicionales --}}
                        <div class="grid grid-cols-2 gap-1.5 pt-2.5 border-t border-zinc-200 dark:border-zinc-700">
                            <div class="flex items-center gap-1.5 text-xs">
                                <x-icon name="map-pin" class="size-3.5 text-zinc-500 flex-shrink-0" />
                                <span class="text-zinc-700 dark:text-zinc-300 font-medium truncate">{{ $vehicle['current_branch'] }}</span>
                            </div>
                            
                            @if($vehicle['speed'] > 0)
                                <div class="flex items-center gap-1.5 text-xs">
                                    <x-icon name="gauge" class="size-3.5 text-zinc-500 flex-shrink-0" />
                                    <span class="text-zinc-700 dark:text-zinc-300 font-medium">{{ number_format($vehicle['speed'], 0) }} km/h</span>
                                </div>
                            @endif

                            @if($vehicle['type'] === 'conductor')
                                <div class="flex items-center gap-1.5 text-xs">
                                    <x-icon name="package" class="size-3.5 text-zinc-500 flex-shrink-0" />
                                    <span class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $vehicle['deliveries_completed'] }} entregas</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-xs">
                                    <x-icon name="star" class="size-3.5 text-yellow-500 flex-shrink-0" />
                                    <span class="text-zinc-700 dark:text-zinc-300 font-medium">{{ number_format($vehicle['rating'], 1) }}</span>
                                </div>
                            @else
                                <div class="flex items-center gap-1.5 text-xs">
                                    <x-icon name="route" class="size-3.5 text-zinc-500 flex-shrink-0" />
                                    <span class="text-zinc-700 dark:text-zinc-300 font-medium">{{ number_format($vehicle['odometer'], 0) }} km</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-xs">
                                    <x-icon name="wrench" class="size-3.5 text-zinc-500 flex-shrink-0" />
                                    <span class="text-zinc-700 dark:text-zinc-300 font-medium">{{ number_format($vehicle['maintenance_km_left'], 0) }} km</span>
                                </div>
                            @endif
                        </div>

                        {{-- Alerta de mantenimiento --}}
                        @if($vehicle['needs_maintenance'])
                            <div class="mt-2.5 p-2 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg flex items-center gap-2">
                                <x-icon name="alert-triangle" class="size-4 text-red-600 dark:text-red-400 flex-shrink-0 animate-pulse" />
                                <span class="text-xs text-red-700 dark:text-red-300 font-bold">Requiere mantenimiento urgente</span>
                            </div>
                        @endif

                        {{-- Última actualización --}}
                        <div class="mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-1 text-zinc-500 dark:text-zinc-500">
                                <x-icon name="clock" class="size-3" />
                                <span>{{ $vehicle['updated_at'] }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="p-4 bg-zinc-100 dark:bg-zinc-800 rounded-full mb-3">
                            <x-icon name="inbox" class="size-12 text-zinc-400 dark:text-zinc-600" />
                        </div>
                        <p class="text-zinc-700 dark:text-zinc-300 font-semibold">No hay dispositivos</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-500 mt-1">Los dispositivos aparecerán cuando se activen</p>
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
.vehicle-marker {
    transition: transform 0.5s ease-out;
}
</style>
@endassets

@script
<script>
(function() {
    const mapState = {
        instance: null,
        markers: {},
        branchCircles: {},
        branchMarkers: {},
        autoRefresh: null,
        isReady: false
    };

    function getStatusColor(status) {
        const colors = {
            'active': '#3b82f6',
            'idle': '#6b7280',
            'charging': '#f59e0b',
            'maintenance': '#ef4444'
        };
        return colors[status] || '#6b7280';
    }

    function getBatteryColor(battery) {
        if (battery >= 70) return '#10b981';
        if (battery >= 40) return '#f59e0b';
        return '#ef4444';
    }

    function createVehicleIcon(vehicle) {
        let color = getStatusColor(vehicle.status);
        if (vehicle.needs_maintenance) color = '#ef4444';
        
        const isMoving = vehicle.speed > 2;
        const pulse = vehicle.status === 'active' ? 'animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;' : '';
        
        const svgIcon = vehicle.type === 'conductor' 
            ? `<svg viewBox="0 0 24 24" fill="white" width="16" height="16"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>`
            : `<svg viewBox="0 0 24 24" fill="white" width="16" height="16"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>`;
        
        return L.divIcon({
            html: `<div style="position:relative;width:36px;height:36px;${pulse}">
                     <svg width="36" height="36" viewBox="0 0 36 36">
                       <circle cx="18" cy="18" r="16" fill="${color}" stroke="white" stroke-width="2" 
                               style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3))"/>
                     </svg>
                     <div style="position:absolute;top:10px;left:10px;">
                       ${svgIcon}
                     </div>
                     ${isMoving ? `
                       <div style="position:absolute;bottom:-18px;left:50%;transform:translateX(-50%);
                                   background:${color};color:white;padding:2px 6px;border-radius:6px;
                                   font-size:9px;font-weight:bold;white-space:nowrap;
                                   box-shadow:0 2px 4px rgba(0,0,0,0.3);">
                         ${Math.round(vehicle.speed)} km/h
                       </div>
                     ` : ''}
                   </div>`,
            className: 'vehicle-marker',
            iconSize: [36, 36],
            iconAnchor: [18, 18],
            popupAnchor: [0, -18]
        });
    }

    function createPopupContent(v) {
        return `
            <div style="min-width:260px;font-family:system-ui;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;padding-bottom:10px;border-bottom:2px solid ${getStatusColor(v.status)};">
                    <div style="width:40px;height:40px;background:${getStatusColor(v.status)};border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        ${v.type === 'conductor' ? `<div style="font-size:13px;color:#666;">${v.driver_name}</div>` : ''}
                    </div>
                </div>
                
                <div style="font-size:13px;color:#374151;line-height:1.8;">
                    <div style="display:flex;justify-content:space-between;margin:8px 0;align-items:center;">
                        <span><strong>Estado:</strong></span>
                        <span style="padding:4px 10px;background:${getStatusColor(v.status)};color:white;
                                     border-radius:8px;font-size:11px;font-weight:700;">
                            ${v.status_label}
                        </span>
                    </div>
                    
                    <div style="margin:10px 0;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                            <strong>Batería:</strong>
                            <span style="font-weight:700;">${v.battery.toFixed(1)}%</span>
                        </div>
                        <div style="background:#e5e7eb;border-radius:999px;height:8px;overflow:hidden;">
                            <div style="background:${getBatteryColor(v.battery)};height:100%;width:${v.battery}%;border-radius:999px;transition:width 0.5s;"></div>
                        </div>
                    </div>
                    
                    <div style="margin:10px 0;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                            <strong>Salud:</strong>
                            <span style="font-weight:700;">${v.battery_health.toFixed(1)}%</span>
                        </div>
                        <div style="background:#e5e7eb;border-radius:999px;height:8px;overflow:hidden;">
                            <div style="background:${v.battery_health >= 80 ? '#10b981' : v.battery_health >= 60 ? '#f59e0b' : '#ef4444'};
                                        height:100%;width:${v.battery_health}%;border-radius:999px;transition:width 0.5s;"></div>
                        </div>
                    </div>
                    
                    ${v.speed > 0 ? `
                    <div style="display:flex;justify-content:space-between;margin:8px 0;">
                        <strong>Velocidad:</strong>
                        <span style="font-weight:700;">${v.speed.toFixed(1)} km/h</span>
                    </div>
                    ` : ''}
                    
                    <div style="margin-top:12px;padding-top:12px;border-top:1px solid #e5e7eb;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                            <div>
                                <div style="font-size:11px;color:#888;">Ubicación</div>
                                <div style="font-weight:600;">${v.current_branch}</div>
                            </div>
                            ${v.target_branch ? `
                            <div>
                                <div style="font-size:11px;color:#888;">Destino</div>
                                <div style="font-weight:600;">${v.target_branch}</div>
                            </div>
                            ` : ''}
                        </div>
                        
                        ${v.type === 'conductor' ? `
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px;">
                            <div>
                                <div style="font-size:11px;color:#888;">Entregas</div>
                                <div style="font-weight:600;">${v.deliveries_completed}</div>
                            </div>
                            <div>
                                <div style="font-size:11px;color:#888;">Rating</div>
                                <div style="font-weight:600;">${v.rating.toFixed(1)} ⭐</div>
                            </div>
                        </div>
                        ` : `
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px;">
                            <div>
                                <div style="font-size:11px;color:#888;">Odómetro</div>
                                <div style="font-weight:600;">${v.odometer.toFixed(1)} km</div>
                            </div>
                            <div>
                                <div style="font-size:11px;color:#888;">Mantenimiento</div>
                                <div style="font-weight:600;">${v.maintenance_km_left.toFixed(0)} km</div>
                            </div>
                        </div>
                        `}
                    </div>
                    
                    ${v.needs_maintenance ? `
                    <div style="margin-top:12px;padding:10px;background:#fee2e2;border:1px solid #fca5a5;
                                border-radius:8px;color:#dc2626;font-size:12px;font-weight:700;text-align:center;">
                        ⚠️ Requiere mantenimiento urgente
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    function loadBranches(branches) {
        if (!mapState.isReady || !mapState.instance) return;
        
        console.log('🏢 Cargando', branches.length, 'sedes');
        
        branches.forEach(branch => {
            const branchId = `branch-${branch.id}`;

            if (!mapState.branchCircles[branchId]) {
                const circle = L.circle([branch.lat, branch.lng], {
                    color: branch.color,
                    fillColor: branch.color,
                    fillOpacity: 0.15,
                    radius: branch.radio,
                    weight: 3,
                    dashArray: '8, 12',
                    opacity: 0.6
                }).addTo(mapState.instance);

                const ocupacionColor = branch.ocupacion_porcentaje > 80 ? '#ef4444' : 
                                      branch.ocupacion_porcentaje > 50 ? '#f59e0b' : '#10b981';

                circle.bindPopup(`
                    <div style="min-width:240px;font-family:system-ui;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;padding-bottom:10px;border-bottom:2px solid ${branch.color};">
                            <div style="font-size:28px;">🏢</div>
                            <div>
                                <div style="font-size:18px;font-weight:700;color:${branch.color};">${branch.nombre}</div>
                                <div style="font-size:12px;color:#888;margin-top:2px;">${branch.descripcion || 'Sede'}</div>
                            </div>
                        </div>
                        <div style="font-size:14px;color:#444;line-height:1.8;">
                            <div style="display:flex;justify-content:space-between;margin:8px 0;">
                                <strong>Radio:</strong>
                                <span>${branch.radio}m</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;margin:8px 0;">
                                <strong>Capacidad:</strong>
                                <span>${branch.dispositivos_actuales} / ${branch.capacidad}</span>
                            </div>
                            <div style="margin:12px 0;">
                                <div style="font-size:12px;color:#666;margin-bottom:4px;">Ocupación</div>
                                <div style="background:#f3f4f6;border-radius:999px;height:10px;overflow:hidden;">
                                    <div style="background:${ocupacionColor};height:100%;width:${branch.ocupacion_porcentaje}%;
                                                border-radius:999px;transition:width 0.5s;"></div>
                                </div>
                                <div style="font-size:12px;color:#888;margin-top:4px;text-align:right;">
                                    ${branch.ocupacion_porcentaje}%
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                mapState.branchCircles[branchId] = circle;

                const marker = L.marker([branch.lat, branch.lng], {
                    icon: L.divIcon({
                        html: `<div style="background:${branch.color};color:white;padding:6px 12px;border-radius:10px;
                                          font-size:12px;font-weight:700;white-space:nowrap;
                                          box-shadow:0 4px 12px rgba(0,0,0,0.25);border:2px solid white;">
                                 <div style="display:flex;align-items:center;gap:6px;">
                                     <span>🏢</span>
                                     <span>${branch.nombre}</span>
                                     <span style="background:rgba(255,255,255,0.3);padding:2px 6px;
                                                  border-radius:6px;font-size:10px;">
                                         ${branch.dispositivos_actuales}
                                     </span>
                                 </div>
                               </div>`,
                        className: '',
                        iconSize: [null, null],
                        iconAnchor: [0, -10]
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

        console.log(`🚗 Actualizando ${vehicles.length} dispositivos`);

        const currentIds = vehicles.map(v => v.device_id);
        
        Object.keys(mapState.markers).forEach(id => {
            if (!currentIds.includes(id)) {
                mapState.instance.removeLayer(mapState.markers[id]);
                delete mapState.markers[id];
            }
        });

        vehicles.forEach(v => {
            if (!v.lat || !v.lng) return;

            const icon = createVehicleIcon(v);
            const popup = createPopupContent(v);

            if (!mapState.markers[v.device_id]) {
                const marker = L.marker([v.lat, v.lng], { icon }).addTo(mapState.instance);
                marker.bindPopup(popup, { maxWidth: 300 });
                mapState.markers[v.device_id] = marker;
                console.log(`✨ Creado: ${v.device_id}`);
            } else {
                const marker = mapState.markers[v.device_id];
                marker.setLatLng([v.lat, v.lng]);
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
            console.log('✅ Mapa inicializado');
            return true;
        } catch (err) {
            console.error('❌ Error inicializando mapa:', err);
            return false;
        }
    }

    function startAutoRefresh(seconds) {
        if (mapState.autoRefresh) clearInterval(mapState.autoRefresh);
        mapState.autoRefresh = setInterval(() => {
            console.log('🔄 Auto-refresh...');
            $wire.call('loadMapData');
        }, seconds * 1000);
    }

    function stopAutoRefresh() {
        if (mapState.autoRefresh) {
            clearInterval(mapState.autoRefresh);
            mapState.autoRefresh = null;
        }
    }

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
            mapState.instance.setView([vehicle.lat, vehicle.lng], 17, { 
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

    Livewire.on('stopAutoRefresh', () => stopAutoRefresh());

    setTimeout(() => {
        console.log('🚀 Inicializando mapa BgaGO...');
        
        if (initMap()) {
            const vehicles = {!! json_encode($vehicles) !!};
            const branches = {!! json_encode($branches) !!};
            
            if (branches && branches.length > 0) {
                setTimeout(() => loadBranches(branches), 300);
            }
            
            if (vehicles && vehicles.length > 0) {
                setTimeout(() => loadVehicles(vehicles), 600);
            }

            @if($autoRefresh)
                setTimeout(() => startAutoRefresh({{ $refreshInterval }}), 1500);
            @endif
        }
    }, 100);
})();
</script>
@endscript
