@php
use App\Enums\MaintenanceStatus;
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="size-16 rounded-lg bg-gradient-to-br from-{{ $maintenance->tipo->color() }}-500 to-{{ $maintenance->tipo->color() }}-600 flex items-center justify-center">
                    <flux:icon.wrench class="size-8 text-white" />
                </div>
                
                <div>
                    <flux:heading size="xl">Mantenimiento #{{ $maintenance->id }}</flux:heading>
                    <flux:subheading>{{ $maintenance->tipo->label() }} - {{ $maintenance->vehicle->placa }}</flux:subheading>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                @if($maintenance->estado === MaintenanceStatus::Programado)
                    <form method="POST" action="{{ route('admin.maintenances.start', $maintenance) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <flux:button type="submit" variant="primary" icon="play">
                            Iniciar
                        </flux:button>
                    </form>
                @endif

                @if($maintenance->estado === MaintenanceStatus::EnProceso)
                    <form method="POST" action="{{ route('admin.maintenances.complete', $maintenance) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <flux:button type="submit" variant="primary" icon="check">
                            Completar
                        </flux:button>
                    </form>
                @endif

                @if($maintenance->canCancel())
                    <form method="POST" action="{{ route('admin.maintenances.cancel', $maintenance) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <flux:button 
                            type="submit" 
                            variant="danger" 
                            icon="x-mark"
                            onclick="return confirm('¿Estás seguro de cancelar este mantenimiento?')"
                        >
                            Cancelar
                        </flux:button>
                    </form>
                @endif

                @if($maintenance->canEdit())
                    <flux:button :href="route('admin.maintenances.edit', $maintenance)" icon="pencil">
                        Editar
                    </flux:button>
                @endif
                
                <flux:button :href="route('admin.maintenances.index')" variant="ghost" icon="arrow-left">
                    Volver
                </flux:button>
            </div>
        </div>

        {{-- Estado --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <flux:heading size="lg" class="mb-4">Estado Actual</flux:heading>
            
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-{{ $maintenance->estado->color() }}-100 text-{{ $maintenance->estado->color() }}-700 dark:bg-{{ $maintenance->estado->color() }}-900/30 dark:text-{{ $maintenance->estado->color() }}-400">
                    <span class="size-2 rounded-full bg-current animate-pulse"></span>
                    {{ $maintenance->estado->label() }}
                </span>
                
                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                    Registrado: {{ $maintenance->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Información Principal --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Vehículo --}}
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Vehículo</flux:heading>
                    
                    <div class="flex items-center gap-4">
                        <div class="size-16 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <flux:icon.truck class="size-8 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <div class="font-mono text-xl font-bold text-zinc-900 dark:text-zinc-100">
                                {{ $maintenance->vehicle->placa }}
                            </div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $maintenance->vehicle->marca }} {{ $maintenance->vehicle->modelo }}
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $maintenance->vehicle->tipo->label() }} - Año {{ $maintenance->vehicle->anio }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Descripción --}}
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Descripción del Trabajo</flux:heading>
                    <p class="text-zinc-700 dark:text-zinc-300 whitespace-pre-line">{{ $maintenance->descripcion }}</p>
                </div>

                {{-- Repuestos --}}
                @if($maintenance->repuestos_usados)
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Repuestos Utilizados</flux:heading>
                    <p class="text-zinc-700 dark:text-zinc-300 whitespace-pre-line">{{ $maintenance->repuestos_usados }}</p>
                </div>
                @endif

                {{-- Observaciones --}}
                @if($maintenance->observaciones)
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Observaciones</flux:heading>
                    <p class="text-zinc-700 dark:text-zinc-300 whitespace-pre-line">{{ $maintenance->observaciones }}</p>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                
                {{-- Tipo --}}
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Tipo</flux:heading>
                    
                    <div class="space-y-3">
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-{{ $maintenance->tipo->color() }}-100 dark:bg-{{ $maintenance->tipo->color() }}-900/30 text-{{ $maintenance->tipo->color() }}-700 dark:text-{{ $maintenance->tipo->color() }}-400 font-medium">
                            {{ $maintenance->tipo->label() }}
                        </span>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $maintenance->tipo->description() }}
                        </p>
                    </div>
                </div>

                {{-- Fechas --}}
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Fechas</flux:heading>
                    
                    <div class="space-y-3">
                        @if($maintenance->fecha_programada)
                        <div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Programada</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $maintenance->fecha_programada->format('d/m/Y') }}
                            </div>
                        </div>
                        @endif
                        
                        @if($maintenance->fecha_realizada)
                        <div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Realizada</div>
                            <div class="font-medium text-green-600 dark:text-green-400">
                                {{ $maintenance->fecha_realizada->format('d/m/Y') }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Kilometraje --}}
                @if($maintenance->kilometraje_actual)
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Kilometraje</flux:heading>
                    
                    <div class="space-y-3">
                        <div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Actual</div>
                            <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                {{ number_format($maintenance->kilometraje_actual, 0, ',', '.') }} km
                            </div>
                        </div>
                        
                        @if($maintenance->kilometraje_proximo)
                        <div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Próximo Mantenimiento</div>
                            <div class="font-medium text-blue-600 dark:text-blue-400">
                                {{ number_format($maintenance->kilometraje_proximo, 0, ',', '.') }} km
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Taller y Mecánico --}}
                @if($maintenance->taller || $maintenance->mecanico)
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Proveedor</flux:heading>
                    
                    <div class="space-y-3">
                        @if($maintenance->taller)
                        <div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Taller</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $maintenance->taller }}
                            </div>
                        </div>
                        @endif
                        
                        @if($maintenance->mecanico)
                        <div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Mecánico</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $maintenance->mecanico }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Costo --}}
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Costo Total</flux:heading>
                    
                    <div class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                        ${{ number_format($maintenance->costo, 0, ',', '.') }}
                    </div>
                    <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        COP
                    </div>
                </div>

                {{-- Registrado por --}}
                @if($maintenance->user)
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                    <flux:heading size="lg" class="mb-4">Registrado por</flux:heading>
                    
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                            <span class="text-purple-600 dark:text-purple-400 font-semibold text-sm">
                                {{ strtoupper(substr($maintenance->user->name, 0, 2)) }}
                            </span>
                        </div>
                        <div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $maintenance->user->name }}
                            </div>
                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $maintenance->user->email }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>
</x-layouts.app>
