@php
    $badge = fn ($s) => match($s) {
        'completada' => 'bg-blue-700 text-white',
        default => 'bg-blue-600 text-white'
    };
    $typeChip = fn ($t) => match($t) {
        'reserva' => 'bg-blue-600/10 text-blue-400 ring-1 ring-blue-500/20',
        'domicilio' => 'bg-orange-600/10 text-orange-400 ring-1 ring-orange-500/20',
        default => 'bg-zinc-600/10 text-zinc-400 ring-1 ring-zinc-500/20'
    };
    $rowClass = fn ($s) => $s === 'cancelada' ? 'bg-zinc-100 dark:bg-zinc-800/60' : '';
    $btnClass = 'inline-flex items-center justify-center w-28 h-9 rounded-lg text-sm ring-1 ring-zinc-300 dark:ring-white/10 text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-white/10 whitespace-nowrap';
    $thumb = function ($vehicle) {
        $v = strtolower($vehicle ?? '');
        if (str_contains($v, 'moto')) return asset('images/catalog/motocicleta.png');
        if (str_contains($v, 'bici')) return asset('images/catalog/bicicleta_electrica.png');
        if (str_contains($v, 'patineta')) return asset('images/catalog/patineta.png');
        if (str_contains($v, 'scooter')) return asset('images/catalog/scooter_electrico.png');
        return asset('images/catalog/motocicleta.png');
    };

    $userId = auth()->id();
    $items = [];
    try {
        $resList = \App\Models\Reservation::with(['vehicle'])
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->orderByDesc('fecha_inicio')
            ->take(6)
            ->get();

        if ($resList->isNotEmpty()) {
            $items = $resList->map(function($r) {
                $paymentId = null;
                try {
                    $p = \App\Models\Payment::where('reserva_id', $r->id)->latest()->first();
                    $paymentId = $p?->codigo_transaccion;
                } catch (\Throwable $e) {}
                return [
                    'id' => $r->id,
                    'codigo' => $r->codigo,
                    'title' => $r->tipo->label(),
                    'vehicle' => optional($r->vehicle)->nombre ?? optional($r->vehicle?->tipo)->label() ?? 'Vehículo',
                    'when' => optional($r->fecha_inicio)?->format('M d • h:i A'),
                    'tipo' => $r->tipo->value,
                    'status' => $r->estado->value,
                    'estado_label' => $r->estado->label(),
                    'monto' => number_format($r->monto_final ?? 0, 0, ',', '.'),
                    'origen' => $r->origen_direccion,
                    'destino' => $r->destino_direccion,
                    'pago_id' => $paymentId,
                ];
            })->toArray();
        }
    } catch (\Throwable $e) {}

    

    $recent = $items[0] ?? null;
    $past = array_slice($items, 1);
    $active = collect($items)->first(function($i){
        $st = strtolower($i['status'] ?? '');
        return in_array($st, ['activa','pendiente','active','pending']);
    });
    $type = strtolower($active['tipo'] ?? '');
    $activeDomicilio = ($active && in_array($type, ['domicilio','delivery'])) ? $active : null;
    $activeReserva = ($active && in_array($type, ['reserva','reservation'])) ? $active : null;
    if ($active) {
        $past = collect($past)->reject(function($i) use($active){ return ($i['codigo'] ?? '') === ($active['codigo'] ?? ''); })->values()->all();
        if ($recent && (($recent['codigo'] ?? '') === ($active['codigo'] ?? ''))) {
            $recent = null;
        }
    }
@endphp

<section class="mt-6" x-data="{ open:false, item:null, confirmCancel:false, canceling:false, error:null }">
    <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-6">
        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">Más reciente</div>
                <div class="relative rounded-xl overflow-hidden ring-1 ring-zinc-200/70 dark:ring-white/10 bg-zinc-50 dark:bg-zinc-800/50">
                    <div class="aspect-square">
                        <img src="{{ asset('images/about/Domicilio.png') }}" alt="Domicilio" class="w-full h-full object-cover" />
                    </div>
                </div>
                @if($activeDomicilio)
                @elseif($recent)
                @endif
            </div>
            <div>
                <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">Pasadas</div>
                <div class="space-y-3">
                    @if(count($items) === 0)
                        <div class="relative overflow-hidden rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-gradient-to-br from-blue-50 via-white to-blue-100 dark:from-blue-950/20 dark:via-zinc-900 dark:to-blue-900/20 p-8 pt-24 pb-16 mx-auto max-w-xl">
                            <div class="text-center">
                                <div class="inline-flex items-center gap-2 px-3 h-8 rounded-full bg-blue-600/10 text-blue-700 dark:text-blue-300 ring-1 ring-blue-500/20 text-xs">Estado</div>
                                <h3 class="mt-3 text-2xl font-bold text-zinc-900 dark:text-white">No hay reservas activas en este momento</h3>
                                <p class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">Reserva un vehículo o solicita un domicilio ahora mismo. Es rápido y sencillo.</p>
                                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                                    <a href="{{ route('catalog.reserve') }}" wire:navigate class="inline-flex items-center justify-center h-11 px-5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm">
                                        <svg class="mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7h11v8H2z"/><path d="M13 10h4l3 3v2h-7z"/><circle cx="5.5" cy="17.5" r="2"/><circle cx="16.5" cy="17.5" r="2"/></svg>
                                        Reservar vehículo
                                    </a>
                                    <a href="{{ route('services.delivery') }}" wire:navigate class="inline-flex items-center justify-center h-11 px-5 rounded-lg bg-white text-zinc-900 hover:bg-zinc-100 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 ring-1 ring-zinc-300 dark:ring-zinc-700 shadow-sm">
                                        <svg class="mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4H9.5a2 2 0 00-1.8 1H8a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V6a2 2 0 00-2-2z"/><path d="M9 8h6"/><path d="M9 12h6"/><path d="M9 16h4"/></svg>
                                        Solicitar domicilio
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                    @if($activeReserva)
                        <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $thumb($activeReserva['vehicle'] ?? null) }}" alt="Vehículo" class="h-12 w-12 rounded-xl object-contain bg-zinc-100 dark:bg-zinc-800 p-1" />
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $activeReserva['title'] }}</div>
                                    <div class="text-xs text-zinc-600 dark:text-zinc-400">{{ $activeReserva['origen'] ?? $activeReserva['vehicle'] }}{{ isset($activeReserva['destino']) ? ' → '.$activeReserva['destino'] : '' }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $activeReserva['when'] }} • COP{{ $activeReserva['monto'] }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center h-6 px-2 rounded text-xs whitespace-nowrap {{ $badge($activeReserva['status']) }}">{{ $activeReserva['estado_label'] ?? ucfirst($activeReserva['status']) }}</span>
                                <span class="inline-flex items-center h-6 px-2 rounded text-xs whitespace-nowrap {{ $typeChip($activeReserva['tipo'] ?? '') }}">{{ ($activeReserva['tipo'] ?? '') === 'domicilio' ? 'Domicilio' : 'Reserva' }}</span>
                                <a href="#" class="{{ $btnClass }}" @click.prevent="open=true; item=@js($activeReserva)">Ver detalles</a>
                            </div>
                        </div>
                    @endif
                    @if($activeDomicilio)
                        <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $thumb($activeDomicilio['vehicle'] ?? null) }}" alt="Vehículo" class="h-12 w-12 rounded-xl object-contain bg-zinc-100 dark:bg-zinc-800 p-1" />
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $activeDomicilio['title'] }}</div>
                                    <div class="text-xs text-zinc-600 dark:text-zinc-400">{{ $activeDomicilio['origen'] ?? $activeDomicilio['vehicle'] }}{{ isset($activeDomicilio['destino']) ? ' → '.$activeDomicilio['destino'] : '' }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $activeDomicilio['when'] }} • COP{{ $activeDomicilio['monto'] }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center h-6 px-2 rounded text-xs whitespace-nowrap {{ $badge($activeDomicilio['status']) }}">{{ $activeDomicilio['estado_label'] ?? ucfirst($activeDomicilio['status']) }}</span>
                                <span class="inline-flex items-center h-6 px-2 rounded text-xs whitespace-nowrap {{ $typeChip($activeDomicilio['tipo'] ?? '') }}">{{ ($activeDomicilio['tipo'] ?? '') === 'domicilio' ? 'Domicilio' : 'Reserva' }}</span>
                                <a href="#" class="{{ $btnClass }}" @click.prevent="open=true; item=@js($activeDomicilio)">Ver detalles</a>
                            </div>
                        </div>
                    @endif
                    @foreach($past as $i)
                        <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-4 flex items-center justify-between gap-4 {{ $rowClass($i['status']) }} min-h-[92px] h-full">
                            <div class="flex items-center gap-3">
                                <img src="{{ $thumb($i['vehicle'] ?? null) }}" alt="Vehículo" class="h-12 w-12 rounded-xl object-contain bg-zinc-100 dark:bg-zinc-800 p-1" />
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $i['title'] }}</div>
                                    <div class="text-xs text-zinc-600 dark:text-zinc-400">{{ $i['origen'] ?? $i['vehicle'] }} {{ isset($i['destino']) ? '→ '.$i['destino'] : '' }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $i['when'] }} • COP{{ $i['monto'] }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center h-6 px-2 rounded text-xs whitespace-nowrap {{ $badge($i['status']) }}">{{ $i['estado_label'] ?? ucfirst($i['status']) }}</span>
                                <span class="inline-flex items-center h-6 px-2 rounded text-xs whitespace-nowrap {{ $typeChip($i['tipo'] ?? '') }}">{{ ($i['tipo'] ?? '') === 'domicilio' ? 'Domicilio' : 'Reserva' }}</span>
                                <a href="#" class="{{ $btnClass }}" @click.prevent="open=true; item=@js($i)">Ver detalles</a>
                            </div>
                        </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div x-show="open" x-cloak class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/50" @click="open=false"></div>
        <div class="relative z-10 max-w-lg mx-auto mt-24 rounded-2xl bg-white dark:bg-zinc-900 ring-1 ring-zinc-200/70 dark:ring-white/10 p-6">
            <template x-if="!confirmCancel">
                <div>
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-semibold text-zinc-900 dark:text-white">Detalles de la reserva</h4>
                        <button class="px-3 py-1 text-sm rounded-lg ring-1 ring-zinc-300 dark:ring-white/10" @click="open=false">Cerrar</button>
                    </div>
                    <div class="mt-4 space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
                        <div><span class="font-medium text-zinc-900 dark:text-white">Tipo:</span> <span x-text="item?.tipo === 'domicilio' ? 'Domicilio' : 'Reserva'"></span></div>
                        <div><span class="font-medium text-zinc-900 dark:text-white">Estado:</span> <span x-text="item?.estado_label ?? (item?.status ?? '')"></span></div>
                        <div><span class="font-medium text-zinc-900 dark:text-white">Código de Reserva:</span> <span x-text="item?.codigo"></span></div>
                        <div><span class="font-medium text-zinc-900 dark:text-white">ID de Pago:</span> <span x-text="item?.pago_id ?? 'SIN PAGO'"></span></div>
                        <div><span class="font-medium text-zinc-900 dark:text-white">Origen:</span> <span x-text="item?.origen ?? item?.vehicle"></span></div>
                        <div><span class="font-medium text-zinc-900 dark:text-white">Destino:</span> <span x-text="item?.destino ?? ''"></span></div>
                        <div><span class="font-medium text-zinc-900 dark:text-white">Fecha:</span> <span x-text="item?.when"></span></div>
                        <div><span class="font-medium text-zinc-900 dark:text-white">Monto:</span> COP<span x-text="item?.monto"></span></div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button class="px-4 h-9 rounded-lg ring-1 ring-zinc-300 dark:ring-white/10 text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-white/10" @click="open=false">Cerrar</button>
                        <button class="px-4 h-9 rounded-lg bg-blue-600 text-white hover:bg-blue-700" @click="confirmCancel=true">Cancelar</button>
                    </div>
                </div>
            </template>
            <template x-if="confirmCancel">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-600/10 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l4-2 4 2 4-2 4 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/></svg>
                        </div>
                        <h4 class="text-lg font-semibold text-zinc-900 dark:text-white">Confirmar cancelación</h4>
                    </div>
                    <p class="mt-3 text-sm text-zinc-700 dark:text-zinc-300">¿Deseas cancelar esta reserva? Esta acción cancelará en el sistema y actualizará esta vista.</p>
                    <template x-if="error">
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400" x-text="error"></p>
                    </template>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button class="px-4 h-9 rounded-lg ring-1 ring-zinc-300 dark:ring-white/10 text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-white/10" @click="confirmCancel=false">Volver</button>
                        <button class="px-4 h-9 rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:bg-blue-400" :disabled="canceling" @click="
                            canceling=true; error=null;
                            fetch(`/api/reservations/${item.id}`, { method:'GET', headers:{'Accept':'application/json'} })
                              .then(r => r.ok ? item.id : Promise.reject('No existe'))
                              .then(() => fetch(`/api/reservations/${item.id}/cancel`, {
                                    method: 'POST',
                                    headers: {
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
                                        'X-XSRF-TOKEN': decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || '')
                                    },
                                    body: JSON.stringify({motivo_cancelacion: 'Cancelada por el usuario'})
                                }))
                              .then(res => {
                                  if (res.ok) { item.status='cancelada'; item.estado_label='Cancelada'; confirmCancel=false; open=false; }
                                  else { return res.json().then(d => { error = d.message || 'No se pudo cancelar'; }); }
                              })
                              .catch(() => { error = 'Error procesando la cancelación'; })
                              .finally(() => { canceling=false; })
                        ">Confirmar</button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</section>