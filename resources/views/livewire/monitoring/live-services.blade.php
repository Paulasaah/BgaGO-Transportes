<div class="h-full flex flex-col bg-white dark:bg-zinc-800 rounded-lg shadow border border-zinc-200 dark:border-zinc-700">
    <!-- Header con Filtros -->
    <div class="flex-shrink-0 p-4 border-b border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center justify-between mb-4">
            <flux:heading size="lg">Servicios Activos</flux:heading>
            <flux:badge variant="primary" size="lg">
                {{ count($services) }}
            </flux:badge>
        </div>

        <div class="flex gap-3">
            <flux:input 
                wire:model.live.debounce.300ms="searchTerm" 
                placeholder="Buscar servicio, usuario o conductor..."
                class="flex-1"
            >
                <x-slot name="iconTrailing">
                    <flux:icon.magnifying-glass />
                </x-slot>
            </flux:input>

            <flux:select wire:model.live="selectedPriority" placeholder="Todas las prioridades" class="w-48">
                <option value="">Todas</option>
                <option value="alta">Alta</option>
                <option value="normal">Normal</option>
                <option value="baja">Baja</option>
            </flux:select>

            @if($searchTerm || $selectedPriority)
                <flux:button wire:click="clearFilters" variant="ghost" size="sm">
                    <flux:icon.x-mark />
                </flux:button>
            @endif
        </div>
    </div>

    <!-- Lista de Servicios -->
    <div class="flex-1 overflow-y-auto divide-y divide-zinc-200 dark:divide-zinc-700">
        @forelse($services as $service)
            <div class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                <!-- Header del Servicio -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <flux:heading size="sm">{{ $service['codigo'] }}</flux:heading>
                            
                            @if($service['tipo'] === 'domicilio')
                                <flux:badge variant="info" size="sm">
                                    <flux:icon.truck class="size-3" />
                                    Domicilio
                                </flux:badge>
                            @else
                                <flux:badge variant="purple" size="sm">
                                    <flux:icon.calendar class="size-3" />
                                    Reserva
                                </flux:badge>
                            @endif

                            @if($service['prioridad'] === 'alta')
                                <flux:badge variant="danger" size="sm">
                                    <flux:icon.exclamation-triangle class="size-3" />
                                    Prioridad Alta
                                </flux:badge>
                            @endif
                        </div>

                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                            Cliente: <strong>{{ $service['usuario'] }}</strong>
                        </div>
                    </div>

                    <flux:button 
                        wire:click="viewServiceDetails({{ $service['id'] }})" 
                        variant="ghost" 
                        size="sm"
                        class="flex-shrink-0"
                    >
                        Ver detalles
                    </flux:button>
                </div>

                <!-- Información del Servicio -->
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Conductor</div>
                        <div class="text-sm font-medium">
                            {{ $service['conductor'] ?? 'Sin asignar' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Vehículo</div>
                        <div class="text-sm font-medium">
                            {{ $service['vehiculo'] ?? 'Sin asignar' }}
                        </div>
                    </div>
                </div>

                <!-- Ubicaciones -->
                <div class="space-y-2 mb-3">
                    <div class="flex items-start gap-2 text-sm">
                        <flux:icon.map-pin class="size-4 text-green-500 mt-0.5 flex-shrink-0" />
                        <div class="flex-1 min-w-0">
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Origen</div>
                            <div class="text-zinc-900 dark:text-white truncate">{{ $service['origen'] }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-2 text-sm">
                        <flux:icon.flag class="size-4 text-red-500 mt-0.5 flex-shrink-0" />
                        <div class="flex-1 min-w-0">
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Destino</div>
                            <div class="text-zinc-900 dark:text-white truncate">{{ $service['destino'] }}</div>
                        </div>
                    </div>
                </div>

                <!-- Barra de Progreso -->
                <div class="mb-3">
                    <div class="flex items-center justify-between text-xs text-zinc-600 dark:text-zinc-400 mb-1">
                        <span>Progreso del viaje</span>
                        <span class="font-semibold">{{ $service['progreso'] }}%</span>
                    </div>
                    <div class="h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                        <div 
                            class="h-full bg-blue-500 transition-all duration-300" 
                            style="width: {{ $service['progreso'] }}%"
                        ></div>
                    </div>
                </div>

                <!-- Footer con Información Adicional -->
                <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400 flex-wrap gap-2">
                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="flex items-center gap-1 whitespace-nowrap">
                            <flux:icon.clock class="size-3" />
                            Inicio: {{ $service['hora_inicio'] }}
                        </div>
                        <div class="flex items-center gap-1 whitespace-nowrap">
                            <flux:icon.arrow-trending-up class="size-3" />
                            ETA: {{ $service['eta'] }}
                        </div>
                        <div class="flex items-center gap-1 whitespace-nowrap">
                            <flux:icon.map class="size-3" />
                            {{ $service['distancia_restante'] }}
                        </div>
                    </div>

                    <x-badges.status :status="$service['status']" size="sm" />
                </div>
            </div>
        @empty
            <div class="flex-1 flex items-center justify-center p-8">
                <div class="text-center text-zinc-500 dark:text-zinc-400">
                    <flux:icon.inbox class="size-12 mx-auto mb-2 opacity-50" />
                    <p>No hay servicios activos en este momento</p>
                </div>
            </div>
        @endforelse
    </div>
</div>