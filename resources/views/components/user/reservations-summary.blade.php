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

    if (empty($items)) {
        $items = [
            [
                'codigo' => 'RES-2025-001',
                'title' => 'Servicio de Domicilio',
                'vehicle' => 'Moto',
                'when' => 'Nov 17 • 5:00 PM',
                'tipo' => 'domicilio',
                'status' => 'activa',
                'estado_label' => 'En Curso',
                'monto' => '15.000',
                'origen' => 'Carrera 19 #35-10, Centro',
                'destino' => 'Calle 48 #29-20, Cañaveral',
                'pago_id' => 'PAY-XXXX',
            ],
            [
                'codigo' => 'RES-2025-002',
                'title' => 'Reserva de Vehículo',
                'vehicle' => 'Bicicleta',
                'when' => 'Nov 18 • 6:30 PM',
                'tipo' => 'reserva',
                'status' => 'confirmada',
                'estado_label' => 'Confirmada',
                'monto' => '20.000',
                'origen' => 'Carrera 36 #48-15, Cabecera',
                'destino' => 'Calle 7 #8-30, Floridablanca',
                'pago_id' => 'PAY-XXXX',
            ],
            [
                'codigo' => 'RES-2025-003',
                'title' => 'Reserva de Vehículo',
                'vehicle' => 'Patineta',
                'when' => 'Nov 19 • 8:00 AM',
                'tipo' => 'reserva',
                'status' => 'pendiente',
                'estado_label' => 'Pendiente',
                'monto' => '10.000',
                'origen' => 'Centro Comercial Cacique',
                'destino' => 'Parque San Pío',
                'pago_id' => 'PAY-XXXX',
            ],
            [
                'codigo' => 'RES-2025-004',
                'title' => 'Servicio de Domicilio',
                'vehicle' => 'Moto',
                'when' => 'Nov 15 • 3:10 PM',
                'tipo' => 'domicilio',
                'status' => 'completada',
                'estado_label' => 'Completada',
                'monto' => '25.000',
                'origen' => 'Sucursal Cabecera',
                'destino' => 'Calle 45 #28-90, Bucaramanga',
                'pago_id' => 'PAY-XXXX',
            ],
            [
                'codigo' => 'RES-2025-005',
                'title' => 'Servicio de Domicilio',
                'vehicle' => 'Moto',
                'when' => 'Nov 14 • 2:05 PM',
                'tipo' => 'domicilio',
                'status' => 'cancelada',
                'estado_label' => 'Cancelada',
                'monto' => '18.000',
                'origen' => 'Sucursal Cañaveral',
                'destino' => 'Calle 105 #30-45, Floridablanca',
                'pago_id' => 'PAY-XXXX',
            ],
        ];
    }

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
                    @if(!$activeDomicilio && !$activeReserva)
                        <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-8 text-center relative overflow-hidden">
                            <div class="absolute inset-0 opacity-10 pointer-events-none" style="background: radial-gradient(600px 200px at 10% 10%, #2563eb 0%, transparent 60%), radial-gradient(400px 160px at 90% 20%, #22d3ee 0%, transparent 60%), radial-gradient(500px 180px at 30% 90%, #f59e0b 0%, transparent 60%);"></div>
                            <div class="relative flex flex-col items-center gap-3">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-blue-600/10 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h18"/><path d="M6 10h12"/><path d="M10 14h4"/><path d="M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
                                </div>
                                <div class="text-lg font-semibold text-zinc-900 dark:text-white">No hay reservas pasadas</div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">¿Listo para tu próxima experiencia? Reserva un vehículo ahora.</p>
                                <a href="{{ route('catalog.reserve') }}" wire:navigate class="mt-2 inline-flex items-center gap-2 px-5 h-11 rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-all shadow hover:shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13l-3 3m0 0l3 3m-3-3h11"/><path d="M21 13V7a2 2 0 0 0-2-2H7L3 7v10a2 2 0 0 0 2 2h6"/></svg>
                                    Reservar vehículo
                                </a>
                            </div>
                        </div>
                        <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-8 text-center relative overflow-hidden">
                            <div class="absolute inset-0 opacity-10 pointer-events-none" style="background: radial-gradient(600px 200px at 90% 10%, #2563eb 0%, transparent 60%), radial-gradient(400px 160px at 15% 25%, #22d3ee 0%, transparent 60%), radial-gradient(500px 180px at 70% 90%, #1d4ed8 0%, transparent 60%);"></div>
                            <div class="relative flex flex-col items-center gap-3">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-blue-600/10 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13V7a2 2 0 0 0-2-2H7L3 7v10a2 2 0 0 0 2 2h6"/><path d="M13 7H7"/><path d="M13 11H7"/><path d="M17 15h-4l-1.5 1.5"/></svg>
                                </div>
                                <div class="text-lg font-semibold text-zinc-900 dark:text-white">¿Necesitas un domicilio?</div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Solicita un servicio de entrega de paquetes de forma rápida.</p>
                                <a href="{{ route('services.delivery') }}" wire:navigate class="mt-2 inline-flex items-center gap-2 px-5 h-11 rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-all shadow hover:shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l4-2 4 2 4-2 4 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/><path d="M9 22h6"/></svg>
                                    Solicitar domicilio
                                </a>
                        </div>
                        </div>
                        @else
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
