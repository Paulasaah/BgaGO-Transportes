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
    const mapState = {
        instance: null,
        markers: {},
        branchCircles: {},
        branchMarkers: {},
        autoRefresh: null,
        isReady: false,
        updateQueue: []
    };

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
                        ${v.type === 'conductor' ? `<div style="font-size:12px;color:#71717a;">${v.driver_name}</div>` : ''}
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
                                <div style="color:#a1a1aa;font-size:10px;text-transform:uppercase;">Mantenimiento</div>
                                <div style="font-weight:600;color:${v.maintenance_km_left <= 100 ? '#ef4444' : '#18181b'};">${v.maintenance_km_left.toFixed(0)} km</div>
                            </div>
                        </div>
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

        // Actualizar o crear marcadores
        vehicles.forEach(v => {
            if (!v.lat || !v.lng) return;

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

    Livewire.on('stopAutoRefresh', () => stopAutoRefresh());

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

            @if($autoRefresh)
                setTimeout(() => startAutoRefresh({{ $refreshInterval }}), 1500);
            @endif
        }
    }, 100);
})();
</script>
@endscript
