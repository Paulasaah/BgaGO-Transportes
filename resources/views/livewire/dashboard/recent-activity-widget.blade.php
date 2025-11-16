<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                <svg class="size-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <flux:heading size="lg">Actividad Reciente</flux:heading>
                <flux:subheading>Últimos eventos del sistema</flux:subheading>
            </div>
        </div>
        
        <button 
            wire:click="loadEvents"
            class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white font-medium">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </button>
    </div>

    {{-- Timeline --}}
    <div class="relative">
        {{-- Línea vertical --}}
        <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-zinc-200 dark:bg-zinc-800"></div>

        {{-- Eventos --}}
        <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
            @forelse($events as $event)
                <div class="relative flex gap-4 group">
                    {{-- Icono --}}
                    <div class="relative z-10 flex-shrink-0">
                        <div class="flex items-center justify-center size-12 rounded-full 
                                    {{ $event['color'] === 'blue' ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}
                                    {{ $event['color'] === 'green' ? 'bg-green-100 dark:bg-green-900/30' : '' }}
                                    {{ $event['color'] === 'orange' ? 'bg-orange-100 dark:bg-orange-900/30' : '' }}
                                    {{ $event['color'] === 'red' ? 'bg-red-100 dark:bg-red-900/30' : '' }}
                                    {{ $event['color'] === 'gray' ? 'bg-zinc-100 dark:bg-zinc-800' : '' }}
                                    border-2 border-white dark:border-zinc-900 shadow-sm
                                    group-hover:scale-110 transition-transform">
                            @if($event['icono'] === 'check')
                                <svg class="size-5 {{ $event['color'] === 'blue' ? 'text-blue-600 dark:text-blue-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            @elseif($event['icono'] === 'play')
                                <svg class="size-5 {{ $event['color'] === 'orange' ? 'text-orange-600 dark:text-orange-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @elseif($event['icono'] === 'flag')
                                <svg class="size-5 {{ $event['color'] === 'green' ? 'text-green-600 dark:text-green-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                                </svg>
                            @elseif($event['icono'] === 'user')
                                <svg class="size-5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            @else
                                <svg class="size-5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>
                    </div>

                    {{-- Contenido --}}
                    <div class="flex-1 pb-4">
                        <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-4 group-hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">
                                    {{ $event['titulo'] }}
                                </h4>
                                <span class="text-xs text-zinc-500 dark:text-zinc-500 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($event['timestamp'])->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $event['descripcion'] }}
                            </p>
                            <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-500">
                                {{ \Carbon\Carbon::parse($event['timestamp'])->format('d/m/Y H:i:s') }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center size-16 rounded-full bg-zinc-100 dark:bg-zinc-800 mb-3">
                        <svg class="size-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-white">
                        No hay actividad reciente
                    </p>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                        Los eventos aparecerán aquí
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Footer --}}
    @if(count($events) > 0)
        <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
            <button 
                wire:click="loadMore"
                class="w-full text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                Cargar más eventos
            </button>
        </div>
    @endif
</div>
