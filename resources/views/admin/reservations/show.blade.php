@php
use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="size-16 rounded-lg bg-gradient-to-br 
                    @if($reservation->tipo === ReservationType::Reserva)
                        from-blue-500 to-blue-600
                    @else
                        from-purple-500 to-purple-600
                    @endif
                    flex items-center justify-center">
                    @if($reservation->tipo === ReservationType::Reserva)
                        <flux:icon.clipboard-document-list class="size-8 text-white" />
                    @else
                        <flux:icon.truck class="size-8 text-white" />
                    @endif
                </div>
                
                <div>
                    <flux:heading size="xl">{{ $reservation->codigo }}</flux:heading>
                    <flux:subheading>{{ $reservation->tipo->label() }} - {{ $reservation->estado->label() }}</flux:subheading>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                @if($reservation->estado !== ReservationStatus::Completada && $reservation->estado !== ReservationStatus::Cancelada)
                    <form method="POST" action="{{ route('admin.reservations.cancel', $reservation) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <flux:button 
                            type="submit" 
                            variant="danger" 
                            icon="x-mark"
                            onclick="return confirm('¿Estás seguro de cancelar esta reserva?')"
                        >
                            Cancelar Reserva
                        </flux:button>
                    </form>

                    @if($reservation->estado !== ReservationStatus::Activa)
                        <form method="POST" action="{{ route('admin.reservations.start', $reservation) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <flux:button 
                                type="submit" 
                                variant="primary" 
                                icon="play"
                            >
                                Activar reserva
                            </flux:button>
                        </form>
                    @endif
                @endif
                
                <flux:button :href="route('admin.map', ['reservation' => $reservation->id])" variant="outline" icon="map">
                    Ver en mapa
                </flux:button>

                <flux:button :href="route('admin.reservations.index')" variant="ghost" icon="arrow-left">
                    Volver
                </flux:button>
            </div>
        </div>

        {{-- Estado y Timeline --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <flux:heading size="lg" class="mb-4">Estado Actual</flux:heading>
            
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium
                    @if($reservation->estado === ReservationStatus::Pendiente)
                        bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                    @elseif($reservation->estado === ReservationStatus::Confirmada)
                        bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                    @elseif($reservation->estado === ReservationStatus::Activa)
                        bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                    @elseif($reservation->estado === ReservationStatus::Completada)
                        bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400
                    @else
                        bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                    @endif
                ">
                    <span class="size-2 rounded-full bg-current animate-pulse"></span>
                    {{ $reservation->estado->label() }}
                </span>
                
                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                    Creada: {{ $reservation->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Información Principal --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Cliente --}}
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Información del Cliente</flux:heading>
                    
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="size-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-semibold text-lg">
                                    {{ strtoupper(substr($reservation->user->name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $reservation->user->name }}
                                </div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $reservation->user->email }}
                                </div>
                                @if($reservation->user->phone)
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        📱 {{ $reservation->user->phone }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fechas y Horarios --}}
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Fechas y Horarios</flux:heading>
                    
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Inicio</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $reservation->fecha_inicio->format('d/m/Y') }}
                            </div>
                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $reservation->fecha_inicio->format('H:i') }}
                            </div>
                        </div>
                        
                        @if($reservation->fecha_fin)
                        <div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Fin</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $reservation->fecha_fin->format('d/m/Y') }}
                            </div>
                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $reservation->fecha_fin->format('H:i') }}
                            </div>
                        </div>
                        @endif
                        
                        @php
                            $duracion = $reservation->fecha_inicio->diff($reservation->fecha_fin ?? now());
                        @endphp
                        <div class="md:col-span-2">
                            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Duración</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                @if($duracion->days > 0)
                                    {{ $duracion->days }} día(s)
                                @endif
                                {{ $duracion->h }} hora(s) {{ $duracion->i }} minuto(s)
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ubicaciones (solo para domicilios) --}}
                @php
                    $hasRealOrigen = $reservation->origen_direccion && $reservation->origen_direccion !== 'Sin dirección';
                    $hasRealDestino = $reservation->destino_direccion && $reservation->destino_direccion !== 'Sin destino';
                @endphp
                @if($hasRealOrigen || $hasRealDestino)
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Ubicaciones</flux:heading>
                    
                    <div class="space-y-4">
                        @if($hasRealOrigen)
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
                                <flux:icon.map-pin class="size-4 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400 mb-0.5">Origen</div>
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100 leading-relaxed">
                                    {{ $reservation->origen_direccion }}
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($hasRealDestino)
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center">
                                <flux:icon.flag class="size-4 text-red-600 dark:text-red-400" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400 mb-0.5">Destino</div>
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100 leading-relaxed">
                                    {{ $reservation->destino_direccion }}
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($reservation->distancia_km || $reservation->duracion_minutos)
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                                <flux:icon.map class="size-4 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400 mb-0.5">Ruta planificada</div>
                                <div class="text-sm text-zinc-700 dark:text-zinc-300">
                                    @if($reservation->distancia_km)
                                        Distancia: <span class="font-medium">{{ $reservation->distancia_km }} km</span>
                                    @endif
                                    @if($reservation->distancia_km && $reservation->duracion_minutos)
                                        <span class="mx-1 text-zinc-400">·</span>
                                    @endif
                                    @if($reservation->duracion_minutos)
                                        Tiempo estimado: <span class="font-medium">{{ $reservation->duracion_minutos }} min</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Mapa de la reserva / domicilio --}}
                @php
                    $hasGeometry = !empty($reservation->waypoints);
                    $hasCoords = $reservation->origen_lat && $reservation->origen_lng && $reservation->destino_lat && $reservation->destino_lng;
                    $hasBranchLocation = $reservation->branch && $reservation->branch->lat && $reservation->branch->lon;
                @endphp
                @if($hasGeometry || $hasCoords || $hasBranchLocation)
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Mapa</flux:heading>
                    <div id="reservation-map" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700" style="height: 320px;"></div>
                </div>
                @endif

                {{-- Observaciones --}}
                @if($reservation->observaciones)
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Observaciones</flux:heading>
                    <p class="text-zinc-700 dark:text-zinc-300">{{ $reservation->observaciones }}</p>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                
                {{-- Vehículo --}}
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Vehículo</flux:heading>
                    
                    @if($reservation->vehicle)
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <flux:icon.truck class="size-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <div class="font-mono font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $reservation->vehicle->placa }}
                                    </div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $reservation->vehicle->marca }} {{ $reservation->vehicle->modelo }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-sm">
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                                    {{ $reservation->vehicle->tipo->label() }}
                                </span>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 italic">Sin vehículo asignado</p>
                    @endif
                </div>

                {{-- Conductor --}}
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Conductor</flux:heading>
                    
                    @if($reservation->driver)
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <span class="text-green-600 dark:text-green-400 font-semibold text-sm">
                                    {{ strtoupper(substr($reservation->driver->name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $reservation->driver->name }}
                                </div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $reservation->driver->email }}
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 italic">Sin conductor asignado</p>
                    @endif
                </div>

                {{-- Sede --}}
                @if($reservation->branch)
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Sede</flux:heading>
                    
                    <div class="font-medium text-zinc-900 dark:text-zinc-100">
                        {{ $reservation->branch->nombre }}
                    </div>
                    @if($reservation->branch->direccion)
                        <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                            {{ $reservation->branch->direccion }}
                        </div>
                    @endif
                </div>
                @endif

                {{-- Monto --}}
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Monto</flux:heading>
                    
                    <div class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                        ${{ number_format($reservation->monto_final ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        COP
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>

@php
    $mapData = [
        'tipo' => $reservation->tipo->value,
        'origen' => [
            'lat' => $reservation->origen_lat,
            'lng' => $reservation->origen_lng,
        ],
        'destino' => [
            'lat' => $reservation->destino_lat,
            'lng' => $reservation->destino_lng,
        ],
        'geometry' => $reservation->waypoints,
        'branch' => $reservation->branch ? [
            'lat' => $reservation->branch->lat,
            'lng' => $reservation->branch->lon,
            'nombre' => $reservation->branch->nombre,
        ] : null,
        'meta' => [
            'codigo' => $reservation->codigo,
            'origen_label' => $reservation->origen_direccion,
            'destino_label' => $reservation->destino_direccion,
        ],
    ];
@endphp

@once
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    >
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""
    ></script>

    <style>
        .leaflet-container .leaflet-popup-content-wrapper {
            border-radius: 0.75rem;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.35);
            background-color: #ffffff; /* light mode: popup blanco */
        }

        .dark .leaflet-container .leaflet-popup-content-wrapper {
            background-color: #020617; /* slate-950: fondo oscuro en modo dark */
        }

        .leaflet-container .leaflet-popup-content {
            margin: 8px 12px;
            font-size: 0.875rem; /* ~text-sm */
            line-height: 1.4;
            color: #111827; /* zinc-900 en light */
        }

        .dark .leaflet-container .leaflet-popup-content {
            color: #E5E7EB; /* zinc-200: texto claro sobre fondo oscuro */
        }
    </style>
@endonce

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('reservation-map');
        if (!container) return;

        const data = @json($mapData);

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        const originIcon = L.divIcon({
            className: 'bg-transparent',
            html: `<div class="shadow-lg rounded-full bg-emerald-500 border-2 border-white text-[11px] font-semibold text-white flex items-center justify-center w-8 h-8">O</div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 16],
        });

        const destIcon = L.divIcon({
            className: 'bg-transparent',
            html: `<div class="shadow-lg rounded-full bg-red-500 border-2 border-white text-[11px] font-semibold text-white flex items-center justify-center w-8 h-8">D</div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 16],
        });

        const branchIcon = L.divIcon({
            className: 'bg-transparent',
            html: `<div class="shadow-lg rounded-full bg-slate-800 border-2 border-white text-[11px] font-semibold text-white flex items-center justify-center w-8 h-8">S</div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 16],
        });

        const defaultCenter = [7.1193, -73.1227];
        const map = L.map('reservation-map', {
            zoomControl: true,
            attributionControl: true,
        }).setView(defaultCenter, 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap',
        }).addTo(map);

        let bounds = null;

        if (data.geometry) {
            const layer = L.geoJSON(data.geometry, {
                style: {
                    color: '#2563eb',
                    weight: 4,
                },
            }).addTo(map);
            bounds = layer.getBounds();
        } else if (data.origen.lat && data.origen.lng && data.destino.lat && data.destino.lng) {
            const line = L.polyline([
                [data.origen.lat, data.origen.lng],
                [data.destino.lat, data.destino.lng],
            ], {
                color: '#2563eb',
                weight: 4,
            }).addTo(map);
            bounds = line.getBounds();
        }

        if (data.origen.lat && data.origen.lng) {
            const marker = L.marker([data.origen.lat, data.origen.lng], {
                title: 'Origen',
                icon: originIcon,
            }).addTo(map);

            if (data.meta && data.meta.origen_label) {
                marker.bindPopup(
                    `<div class="popup-meta">
                        <div class="popup-title">Origen</div>
                        <div class="popup-body">${escapeHtml(data.meta.origen_label)}</div>
                    </div>`
                );
            }
        }

        if (data.destino.lat && data.destino.lng) {
            const marker = L.marker([data.destino.lat, data.destino.lng], {
                title: 'Destino',
                icon: destIcon,
            }).addTo(map);

            if (data.meta && data.meta.destino_label) {
                marker.bindPopup(
                    `<div class="popup-meta">
                        <div class="popup-title">Destino</div>
                        <div class="popup-body">${escapeHtml(data.meta.destino_label)}</div>
                    </div>`
                );
            }
        }

        if (!bounds && data.branch && data.branch.lat && data.branch.lng) {
            const marker = L.marker([data.branch.lat, data.branch.lng], {
                title: data.branch.nombre || 'Sede',
                icon: branchIcon,
            }).addTo(map);

            if (data.branch.nombre) {
                marker.bindPopup(
                    `<div class="popup-meta">
                        <div class="popup-title">Sede</div>
                        <div class="popup-body">${escapeHtml(data.branch.nombre)}</div>
                    </div>`
                );
            }

            bounds = marker.getBounds ? marker.getBounds() : null;
        }

        if (bounds && bounds.isValid && bounds.isValid()) {
            map.fitBounds(bounds, { padding: [20, 20] });
        } else {
            map.setView(defaultCenter, 13);
        }
    });
</script>
