<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                <svg class="size-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <flux:heading size="lg">Alertas Activas</flux:heading>
                <flux:subheading>Requieren atención inmediata</flux:subheading>
            </div>
        </div>
        
        @if($alertCount > 0)
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center size-8 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-sm font-bold">
                    {{ $alertCount }}
                </span>
            </div>
        @endif
    </div>

    {{-- Alertas --}}
    <div class="space-y-3 min-h-[200px]">
        @forelse($alerts as $alert)
            <div class="flex items-start gap-3 p-4 rounded-lg border transition-all hover:shadow-md
                        {{ $alert['severidad'] === 'error' ? 'border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/10' : '' }}
                        {{ $alert['severidad'] === 'warning' ? 'border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-900/10' : '' }}
                        {{ $alert['severidad'] === 'info' ? 'border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/10' : '' }}">
                
                {{-- Icono --}}
                <div class="flex-shrink-0 mt-0.5">
                    @if($alert['severidad'] === 'error')
                        <svg class="size-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @elseif($alert['severidad'] === 'warning')
                        <svg class="size-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    @else
                        <svg class="size-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @endif
                </div>

                {{-- Contenido --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">
                                {{ $alert['titulo'] }}
                            </h4>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                {{ $alert['mensaje'] }}
                            </p>
                        </div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-500 whitespace-nowrap">
                            {{ $alert['timestamp'] }}
                        </span>
                    </div>
                </div>

                {{-- Botón cerrar --}}
                <button 
                    wire:click="dismissAlert('{{ $alert['id'] }}')"
                    class="flex-shrink-0 p-1 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors"
                    title="Descartar">
                    <svg class="size-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @empty
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center size-16 rounded-full bg-green-100 dark:bg-green-900/30 mb-3">
                    <svg class="size-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-zinc-900 dark:text-white">
                    Todo está en orden
                </p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                    No hay alertas activas en este momento
                </p>
            </div>
        @endforelse
    </div>

    {{-- Footer con acciones --}}
    @if($alertCount > 0)
        <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center justify-between text-sm">
                <span class="text-zinc-600 dark:text-zinc-400">
                    {{ $alertCount }} {{ $alertCount === 1 ? 'alerta activa' : 'alertas activas' }}
                </span>
                <button 
                    wire:click="loadAlerts"
                    class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                    Actualizar
                </button>
            </div>
        </div>
    @endif
</div>
