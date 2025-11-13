<x-layouts.public>
    <div x-data="{ open: false, selected: null, show(res) { this.selected = res; this.open = true }, close() { this.open = false; this.selected = null } }" class="py-24 min-h-screen bg-gradient-to-br from-white via-blue-50 to-blue-200 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-bold text-blue-600 dark:text-blue mb-4">
                    Mis Reservas
                </h1>
                <p class="text-lg text-blue-600 dark:text-blue-400">
                    Gestiona y consulta el historial de tus reservas
                </p>
            </div>

            @if(session('status'))
                <div class="mb-6 rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-950/30 px-4 py-3 text-green-800 dark:text-green-300 flex items-start gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">{{ session('status') }}</span>
                </div>
            @endif

            @php
                $reservasQuery = auth()->user()?->reservations()
                    ->with(['vehicle', 'branch'])
                    ->orderByDesc('created_at');
                $estadoParam = request('estado');
                if ($estadoParam && in_array($estadoParam, \App\Enums\ReservationStatus::toArray())) {
                    $reservasQuery->where('estado', $estadoParam);
                }
                $reservas = $reservasQuery->get() ?? collect();
                $total = auth()->user()?->reservations()->count() ?? 0;
                $activas = auth()->user()?->reservations()->activas()->count() ?? 0;
                $completadas = auth()->user()?->reservations()->completadas()->count() ?? 0;
                $canceladas = auth()->user()?->reservations()->canceladas()->count() ?? 0;
            @endphp
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 p-6 mb-8">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('mis-reservas') }}" class="px-4 py-2 rounded-lg font-medium transition-colors {{ request('estado') ? 'bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-600' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                        Todas ({{ $total }})
                    </a>
                    <a href="{{ route('mis-reservas', ['estado' => 'activa']) }}" class="px-4 py-2 rounded-lg font-medium transition-colors {{ request('estado') === 'activa' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-600' }}">
                        Activas ({{ $activas }})
                    </a>
                    <a href="{{ route('mis-reservas', ['estado' => 'completada']) }}" class="px-4 py-2 rounded-lg font-medium transition-colors {{ request('estado') === 'completada' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-600' }}">
                        Completadas ({{ $completadas }})
                    </a>
                    <a href="{{ route('mis-reservas', ['estado' => 'cancelada']) }}" class="px-4 py-2 rounded-lg font-medium transition-colors {{ request('estado') === 'cancelada' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-600' }}">
                        Canceladas ({{ $canceladas }})
                    </a>
                </div>
            </div>

            <div class="space-y-6">
                @forelse($reservas as $reserva)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden hover:shadow-xl transition-shadow {{ $reserva->isCancelada() ? 'opacity-60' : ($reserva->isCompletada() ? 'opacity-75' : '') }}">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="flex items-start gap-4 flex-1">
                                    @php
                                        $isDom = $reserva->isDomicilio();
                                        $cardColor = $isDom ? $reserva->tipo->color() : ($reserva->vehicle?->tipo?->color() ?? 'blue');
                                    @endphp
                                    <div class="w-16 h-16 bg-{{ $cardColor }}-100 dark:bg-{{ $cardColor }}-950/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                        @if($isDom)
                                            <svg class="w-8 h-8 text-{{ $cardColor }}-600 dark:text-{{ $cardColor }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        @else
                                            <svg class="w-8 h-8 text-{{ $cardColor }}-600 dark:text-{{ $cardColor }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h3 class="text-xl font-bold text-zinc-900 dark:text-white">{{ $isDom ? 'Domicilio' : ($reserva->vehicle?->tipo?->label() ?? 'Vehículo') }}</h3>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $reserva->estado->color() }}-100 text-{{ $reserva->estado->color() }}-700 dark:bg-{{ $reserva->estado->color() }}-950/70 dark:text-{{ $reserva->estado->color() }}-400">
                                                @if($reserva->estado->isActive())
                                                    <span class="w-2 h-2 bg-{{ $reserva->estado->color() }}-500 rounded-full mr-1.5 animate-pulse"></span>
                                                @endif
                                                {{ $reserva->estado->label() }}
                                            </span>
                                        </div>
                                        @if($isDom)
                                            <div class="grid sm:grid-cols-2 gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    </svg>
                                                    <span>{{ $reserva->origen_direccion ?? '—' }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                                    </svg>
                                                    <span>{{ $reserva->destino_direccion ?? '—' }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span>{{ $reserva->notas_cliente ?? '—' }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span class="font-semibold">${{ number_format($reserva->monto_final, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="grid sm:grid-cols-2 gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span>{{ optional($reserva->fecha_inicio)->format('d/m/Y - H:i') }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span>
                                                        @php $mins = $reserva->duracion_minutos ?? $reserva->getDuracionEstimada(); @endphp
                                                        {{ $mins >= 60 ? floor($mins/60).' horas' : $mins.' min' }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    </svg>
                                                    <span>{{ $reserva->branch?->nombre ?? '—' }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.11 0-2.08.402-2.599 1M12 8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span class="font-semibold">${{ number_format($reserva->monto_final, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        @endif
                                        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">ID: {{ $reserva->codigo }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2 sm:flex-row md:flex-col">
                                    @php
                                        $detalle = [
                                            'id' => $reserva->id,
                                            'codigo' => $reserva->codigo,
                                            'tipo' => $reserva->isDomicilio() ? 'domicilio' : 'punto',
                                            'vehiculo' => $reserva->vehicle?->tipo?->label(),
                                            'estado_label' => $reserva->estado->label(),
                                            'monto' => $reserva->monto_final,
                                            'fecha_inicio' => optional($reserva->fecha_inicio)->format('d/m/Y - H:i'),
                                            'duracion_minutos' => $reserva->duracion_minutos ?? $reserva->getDuracionEstimada(),
                                            'branch' => $reserva->branch?->nombre,
                                            'origen' => $reserva->origen_direccion,
                                            'destino' => $reserva->destino_direccion,
                                            'notas' => $reserva->notas_cliente,
                                            'route_pago' => route('pago', ['reserva' => $reserva->id]),
                                        ];
                                    @endphp
                                    <button type="button" data-details='@json($detalle)' @click="show(JSON.parse($el.dataset.details))" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-sm whitespace-nowrap text-center">
                                        Ver Detalle
                                    </button>
                                    @if($reserva->canBeCancelled())
                                        <form method="POST" action="{{ route('mis-reservas.cancel', ['reservation' => $reserva->id]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-950/30 dark:hover:bg-red-950/50 dark:text-red-400 rounded-lg font-medium transition-colors text-sm whitespace-nowrap">
                                                Cancelar Reserva
                                            </button>
                                        </form>
                                    @elseif($reserva->isFinal())
                                        <a href="{{ route('catalogo') }}" class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-950/30 dark:hover:bg-blue-950/50 dark:text-blue-400 rounded-lg font-medium transition-colors text-sm whitespace-nowrap text-center">
                                            Reservar de Nuevo
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 p-6 text-center">
                        <div class="text-zinc-600 dark:text-zinc-400">No tienes reservas aún.</div>
                        <a href="{{ route('catalogo') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">Explorar Vehículos</a>
                    </div>
                @endforelse
            </div>

            <!-- CTA para nueva reserva -->
            <div class="mt-12 text-center bg-gradient-to-r from-blue-50 to-blue-50 dark:from-blue-950/60 dark:to-blue-950/60 rounded-2xl p-12 border border-blue-200 dark:border-blue-800">
                <h3 class="text-3xl font-bold text-zinc-900 dark:text-white mb-4">
                    ¿Listo para tu próxima aventura?
                </h3>
                <p class="text-lg text-zinc-600 dark:text-zinc-400 mb-8 max-w-2xl mx-auto">
                    Reserva tu próximo vehículo ecológico y disfruta de la ciudad de una manera diferente
                </p>
                <a href="{{ route('catalogo') }}" class="inline-flex items-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-lg transition-all hover:scale-105 shadow-lg">
                    Explorar Vehículos
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>

        </div>

        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" @click="close()"></div>
            <div class="relative bg-white dark:bg-zinc-800 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-700 w-full max-w-2xl mx-4">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Detalle de Reserva</h3>
                        <button type="button" @click="close()" class="px-3 py-1.5 rounded-md bg-zinc-100 hover:bg-zinc-200 text-zinc-700 dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:text-zinc-300 text-sm">Cerrar</button>
                    </div>

                    <template x-if="selected">
                        <div class="space-y-4 text-sm">
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-zinc-500 dark:text-zinc-400 mb-1">Código</p>
                                    <p class="font-semibold text-zinc-900 dark:text-white" x-text="selected.codigo"></p>
                                </div>
                                <div>
                                    <p class="text-zinc-500 dark:text-zinc-400 mb-1">Estado</p>
                                    <p class="font-semibold text-zinc-900 dark:text-white" x-text="selected.estado_label"></p>
                                </div>
                                <div>
                                    <p class="text-zinc-500 dark:text-zinc-400 mb-1">Tipo</p>
                                    <p class="font-semibold text-zinc-900 dark:text-white" x-text="selected.tipo === 'domicilio' ? 'Entrega a Domicilio' : 'Recoger en Punto'"></p>
                                </div>
                                <div>
                                    <p class="text-zinc-500 dark:text-zinc-400 mb-1">Vehículo</p>
                                    <p class="font-semibold text-zinc-900 dark:text-white" x-text="selected.vehiculo ?? '—'"></p>
                                </div>
                                <div>
                                    <p class="text-zinc-500 dark:text-zinc-400 mb-1">Fecha y Hora</p>
                                    <p class="font-semibold text-zinc-900 dark:text-white" x-text="selected.fecha_inicio ?? '—'"></p>
                                </div>
                                <div>
                                    <p class="text-zinc-500 dark:text-zinc-400 mb-1">Duración</p>
                                    <p class="font-semibold text-zinc-900 dark:text-white" x-text="selected.duracion_minutos >= 60 ? Math.floor(selected.duracion_minutos/60) + ' horas' : (selected.duracion_minutos ?? 0) + ' min'"></p>
                                </div>
                            </div>

                            <div class="grid sm:grid-cols-2 gap-4" x-show="selected.tipo === 'domicilio'">
                                <div>
                                    <p class="text-zinc-500 dark:text-zinc-400 mb-1">Origen</p>
                                    <p class="font-semibold text-zinc-900 dark:text-white" x-text="selected.origen ?? '—'"></p>
                                </div>
                                <div>
                                    <p class="text-zinc-500 dark:text-zinc-400 mb-1">Destino</p>
                                    <p class="font-semibold text-zinc-900 dark:text-white" x-text="selected.destino ?? '—'"></p>
                                </div>
                            </div>

                            <div class="grid sm:grid-cols-2 gap-4" x-show="selected.tipo === 'punto'">
                                <div>
                                    <p class="text-zinc-500 dark:text-zinc-400 mb-1">Sucursal</p>
                                    <p class="font-semibold text-zinc-900 dark:text-white" x-text="selected.branch ?? '—'"></p>
                                </div>
                            </div>

                            <div>
                                <p class="text-zinc-500 dark:text-zinc-400 mb-1">Notas</p>
                                <p class="font-semibold text-zinc-900 dark:text-white" x-text="selected.notas ?? '—'"></p>
                            </div>

                            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-bold text-zinc-900 dark:text-white">Total</span>
                                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="new Intl.NumberFormat('es-CO').format(selected.monto)"></span>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 pt-4">
                                <a :href="selected.route_pago" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-sm">Ir a Pago</a>
                                <button type="button" @click="close()" class="px-4 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:text-zinc-300 rounded-lg font-medium text-sm">Cerrar</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>
