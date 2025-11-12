<div class="bg-white dark:bg-zinc-800 rounded-lg shadow border border-zinc-200 dark:border-zinc-700 flex flex-col max-h-[500px]">
    <div class="flex-shrink-0 p-4 border-b border-zinc-200 dark:border-zinc-700">
        <flux:heading size="lg">Estado de Flota</flux:heading>
    </div>

    {{-- Resumen de Estados --}}
    <div class="flex-shrink-0 grid grid-cols-2 gap-3 p-4 border-b border-zinc-200 dark:border-zinc-700">
        <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-1">
                {{ $summary['activos'] }}
            </div>
            <div class="text-xs text-zinc-600 dark:text-zinc-400">En Servicio</div>
        </div>

        <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-1">
                {{ $summary['disponibles'] }}
            </div>
            <div class="text-xs text-zinc-600 dark:text-zinc-400">Disponibles</div>
        </div>

        <div class="text-center p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mb-1">
                {{ $summary['mantenimiento'] }}
            </div>
            <div class="text-xs text-zinc-600 dark:text-zinc-400">Mantenimiento</div>
        </div>

        <div class="text-center p-3 bg-zinc-50 dark:bg-zinc-700/50 rounded-lg">
            <div class="text-2xl font-bold text-zinc-600 dark:text-zinc-400 mb-1">
                {{ count($vehicles) }}
            </div>
            <div class="text-xs text-zinc-600 dark:text-zinc-400">Total</div>
        </div>
    </div>

    {{-- Lista de Vehículos --}}
    <div class="flex-1 divide-y divide-zinc-200 dark:divide-zinc-700 overflow-y-auto">
        @forelse($vehicles as $vehicle)
            <div class="p-3 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <div class="size-8 bg-zinc-100 dark:bg-zinc-700 rounded-lg flex items-center justify-center flex-shrink-0">
                            <flux:icon.truck class="size-4 text-zinc-600 dark:text-zinc-400" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <flux:heading size="sm" class="truncate">{{ $vehicle['placa'] }}</flux:heading>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                                {{ $vehicle['conductor'] ?? 'Sin conductor' }}
                            </div>
                        </div>
                    </div>

                    <x-badges.status :status="$vehicle['status']" size="sm" class="flex-shrink-0" />
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <div class="text-zinc-500 dark:text-zinc-400">Velocidad</div>
                        <div class="font-medium text-zinc-900 dark:text-white">
                            {{ $vehicle['velocidad'] ?? 0 }} km/h
                        </div>
                    </div>

                    <div>
                        <div class="text-zinc-500 dark:text-zinc-400">Rumbo</div>
                        <div class="font-medium text-zinc-900 dark:text-white">
                            {{ $vehicle['rumbo'] ?? '-' }}
                        </div>
                    </div>
                </div>

                @if($vehicle['status'] === 'busy' && isset($vehicle['servicio_activo']))
                    <div class="mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400">
                            <flux:icon.arrow-path class="size-3 flex-shrink-0" />
                            <span class="truncate">{{ $vehicle['servicio_activo'] }}</span>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="flex-1 flex items-center justify-center p-8">
                <div class="text-center text-zinc-500 dark:text-zinc-400">
                    <flux:icon.truck class="size-12 mx-auto mb-2 opacity-50" />
                    <p class="text-sm">No hay vehículos en monitoreo</p>
                </div>
            </div>
        @endforelse
    </div>
</div>