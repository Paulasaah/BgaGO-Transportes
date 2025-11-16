<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <svg class="size-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <flux:heading size="lg">Servicios Activos</flux:heading>
                <flux:subheading>En curso ahora mismo</flux:subheading>
            </div>
        </div>
        
        @if($serviceCount > 0)
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-sm font-semibold">
                    <span class="relative flex size-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full size-2 bg-blue-500"></span>
                    </span>
                    {{ $serviceCount }} activos
                </span>
            </div>
        @endif
    </div>

    {{-- Lista de servicios --}}
    <div class="space-y-3 max-h-[400px] overflow-y-auto">
        @forelse($services as $service)
            <div class="p-4 rounded-lg border border-zinc-200 dark:border-zinc-800 hover:shadow-md transition-all bg-zinc-50 dark:bg-zinc-800/50">
                {{-- Header del servicio --}}
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <div class="size-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm">
                                {{ substr($service['vehiculo'] ?? 'V', 0, 1) }}
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-white">
                                    {{ $service['codigo'] }}
                                </h4>
                                @if($service['prioridad'] === 'alta')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-medium">
                                        <svg class="size-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Urgente
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                {{ $service['tipo'] }} • {{ $service['usuario'] }}
                            </p>
                        </div>
                    </div>
                    <span class="text-xs text-zinc-500 dark:text-zinc-500 whitespace-nowrap">
                        {{ $service['hora_inicio'] }}
                    </span>
                </div>

                {{-- Información del servicio --}}
                <div class="space-y-2 mb-3">
                    <div class="flex items-start gap-2 text-sm">
                        <svg class="size-4 text-zinc-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-zinc-600 dark:text-zinc-400">
                            <span class="font-medium text-zinc-900 dark:text-white">Conductor:</span> {{ $service['conductor'] }}
                        </span>
                    </div>
                    <div class="flex items-start gap-2 text-sm">
                        <svg class="size-4 text-zinc-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-zinc-600 dark:text-zinc-400 line-clamp-1">
                            {{ $service['origen'] }}
                        </span>
                    </div>
                    @if($service['destino'] !== 'Sin destino')
                        <div class="flex items-start gap-2 text-sm">
                            <svg class="size-4 text-zinc-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            <span class="text-zinc-600 dark:text-zinc-400 line-clamp-1">
                                {{ $service['destino'] }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Barra de progreso --}}
                <div class="mb-3">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="text-zinc-600 dark:text-zinc-400 font-medium">Progreso</span>
                        <div class="flex items-center gap-2">
                            <span class="text-zinc-900 dark:text-white font-semibold">{{ $service['progreso'] }}%</span>
                            <span class="text-zinc-500">•</span>
                            <span class="text-zinc-600 dark:text-zinc-400">ETA: {{ $service['eta'] }}</span>
                        </div>
                    </div>
                    <div class="h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                        <div 
                            class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-500"
                            style="width: {{ $service['progreso'] }}%">
                        </div>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="flex items-center gap-2">
                    <button 
                        wire:click="viewOnMap({{ $service['id'] }})"
                        class="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Ver en Mapa
                    </button>
                    <a 
                        href="{{ route('admin.reservations.show', $service['id']) }}"
                        class="px-3 py-2 border border-zinc-300 dark:border-zinc-600 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Detalles
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center size-16 rounded-full bg-zinc-100 dark:bg-zinc-800 mb-3">
                    <svg class="size-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-zinc-900 dark:text-white">
                    No hay servicios activos
                </p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                    Todos los servicios han sido completados
                </p>
            </div>
        @endforelse
    </div>

    {{-- Footer --}}
    @if($serviceCount > 0)
        <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <a 
                    href="{{ route('admin.map') }}"
                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                    Ver todos en mapa →
                </a>
                <button 
                    wire:click="loadServices"
                    class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white font-medium">
                    Actualizar
                </button>
            </div>
        </div>
    @endif
</div>
