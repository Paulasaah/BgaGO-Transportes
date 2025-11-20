@php
    $badge = fn ($s) => match($s) {
        'completada' => 'bg-emerald-600 text-white',
        'confirmada' => 'bg-blue-600 text-white',
        'pendiente' => 'bg-yellow-500 text-black',
        'cancelada' => 'bg-red-600 text-white',
        default => 'bg-blue-600 text-white'
    };
    $typeChip = fn ($t) => match($t) {
        'reserva' => 'bg-blue-600/10 text-blue-400 ring-1 ring-blue-500/20',
        'domicilio' => 'bg-orange-600/10 text-orange-400 ring-1 ring-orange-500/20',
        default => 'bg-zinc-600/10 text-zinc-400 ring-1 ring-zinc-500/20'
    };
    $rowClass = fn ($s) => $s === 'cancelada' ? 'bg-zinc-100 dark:bg-zinc-800/60' : '';
    $btnClass = 'inline-flex items-center justify-center h-10 px-4 rounded-lg text-sm ring-1 ring-zinc-300 dark:ring-white/10 text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-white/10 whitespace-nowrap';
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
            ->take(12)
            ->get();

        if ($resList->isNotEmpty()) {
            $items = $resList->map(function($r) {
                $paymentId = null;
                try {
                    $p = \App\Models\Payment::where('reserva_id', $r->id)->latest()->first();
                    $paymentId = $p?->codigo_transaccion;
                } catch (\Throwable $e) {}
                return [
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
            [ 'codigo' => 'RES-2025-001','title' => 'Servicio de Domicilio','vehicle' => 'Moto','when' => 'Nov 17 • 5:00 PM','tipo' => 'domicilio','status' => 'activa','estado_label' => 'En Curso','monto' => '15.000','origen' => 'Carrera 19 #35-10, Centro','destino' => 'Calle 48 #29-20, Cañaveral','pago_id' => 'PAY-XXXX' ],
            [ 'codigo' => 'RES-2025-002','title' => 'Reserva de Vehículo','vehicle' => 'Bicicleta','when' => 'Nov 18 • 6:30 PM','tipo' => 'reserva','status' => 'confirmada','estado_label' => 'Confirmada','monto' => '20.000','origen' => 'Carrera 36 #48-15, Cabecera','destino' => 'Calle 7 #8-30, Floridablanca','pago_id' => 'PAY-XXXX' ],
            [ 'codigo' => 'RES-2025-003','title' => 'Reserva de Vehículo','vehicle' => 'Patineta','when' => 'Nov 19 • 8:00 AM','tipo' => 'reserva','status' => 'pendiente','estado_label' => 'Pendiente','monto' => '10.000','origen' => 'Centro Comercial Cacique','destino' => 'Parque San Pío','pago_id' => 'PAY-XXXX' ],
            [ 'codigo' => 'RES-2025-004','title' => 'Servicio de Domicilio','vehicle' => 'Moto','when' => 'Nov 15 • 3:10 PM','tipo' => 'domicilio','status' => 'completada','estado_label' => 'Completada','monto' => '25.000','origen' => 'Sucursal Cabecera','destino' => 'Calle 45 #28-90, Bucaramanga','pago_id' => 'PAY-XXXX' ],
            [ 'codigo' => 'RES-2025-005','title' => 'Servicio de Domicilio','vehicle' => 'Moto','when' => 'Nov 14 • 2:05 PM','tipo' => 'domicilio','status' => 'cancelada','estado_label' => 'Cancelada','monto' => '18.000','origen' => 'Sucursal Cañaveral','destino' => 'Calle 105 #30-45, Floridablanca','pago_id' => 'PAY-XXXX' ],
        ];
    }

    $recent = $items[0] ?? null;
    $active = collect($items)->filter(fn($i)=> in_array(strtolower($i['status'] ?? ''), ['activa','pendiente','active','pending']))->values()->all();
    $completed = collect($items)->filter(fn($i)=> strtolower($i['status'] ?? '') === 'completada')->values()->all();
    $counts = [
        'activas' => collect($active)->count(),
        'pendientes' => collect($items)->where('status','pendiente')->count(),
        'completadas' => collect($items)->where('status','completada')->count(),
    ];
@endphp

<section class="mt-6" x-data="{ open:false, item:null, confirmCancel:false, tab:'activas', filter:'' }">
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-3">
        <div class="rounded-xl bg-white/70 dark:bg-white/5 ring-1 ring-zinc-200/70 dark:ring-white/10 p-4 backdrop-blur">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-emerald-100 dark:bg-emerald-900/30"><svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div><div class="text-sm text-zinc-600 dark:text-zinc-400">Activas</div><div class="text-xl font-bold">{{ $counts['activas'] }}</div></div>
            </div>
        </div>
        <div class="rounded-xl bg-white/70 dark:bg-white/5 ring-1 ring-zinc-200/70 dark:ring-white/10 p-4 backdrop-blur">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-yellow-100 dark:bg-yellow-900/30"><svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div><div class="text-sm text-zinc-600 dark:text-zinc-400">Pendientes</div><div class="text-xl font-bold">{{ $counts['pendientes'] }}</div></div>
            </div>
        </div>
        <div class="rounded-xl bg-white/70 dark:bg-white/5 ring-1 ring-zinc-200/70 dark:ring-white/10 p-4 backdrop-blur">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/30"><svg class="w-5 h-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
                <div><div class="text-sm text-zinc-600 dark:text-zinc-400">Completadas</div><div class="text-xl font-bold">{{ $counts['completadas'] }}</div></div>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="inline-flex rounded-lg ring-1 ring-zinc-200 dark:ring-zinc-800 overflow-hidden">
                <button class="px-4 h-10 text-sm" :class="tab==='activas' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300'" @click="tab='activas'">Activas</button>
                <button class="px-4 h-10 text-sm" :class="tab==='pasadas' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300'" @click="tab='pasadas'">Pasadas</button>
            </div>
            <div class="relative">
                <input x-model="filter" type="text" placeholder="Buscar por origen/destino" class="h-10 w-64 rounded-lg bg-zinc-50 dark:bg-white/10 text-sm px-3 ring-1 ring-zinc-200 dark:ring-white/10 focus:ring-2 focus:ring-blue-600" />
            </div>
        </div>

        <div class="mt-4 space-y-3" x-show="tab==='activas'">
            @forelse($active as $i)
                <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ $thumb($i['vehicle'] ?? null) }}" alt="Vehículo" class="h-12 w-12 rounded-xl object-contain bg-zinc-100 dark:bg-zinc-800 p-1" />
                        <div>
                            <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $i['title'] }}</div>
                            <div class="text-xs text-zinc-600 dark:text-zinc-400">{{ $i['origen'] ?? $i['vehicle'] }}{{ isset($i['destino']) ? ' → '.$i['destino'] : '' }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $i['when'] }} • COP{{ $i['monto'] }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center h-6 px-2 rounded text-xs whitespace-nowrap {{ $badge($i['status']) }}">{{ $i['estado_label'] ?? ucfirst($i['status']) }}</span>
                        <span class="inline-flex items-center h-6 px-2 rounded text-xs whitespace-nowrap {{ $typeChip($i['tipo'] ?? '') }}">{{ ($i['tipo'] ?? '') === 'domicilio' ? 'Domicilio' : 'Reserva' }}</span>
                        <button class="{{ $btnClass }}" @click="open=true; item=@js($i)">Ver detalles</button>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-8 text-center">No hay reservas activas</div>
            @endforelse
        </div>

        <div class="mt-4 space-y-3" x-show="tab==='pasadas'">
            @forelse($completed as $i)
                <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-4 flex items-center justify-between gap-4 {{ $rowClass($i['status']) }}">
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
                        <button class="{{ $btnClass }}" @click="open=true; item=@js($i)">Ver detalles</button>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-8 text-center">No hay reservas completadas</div>
            @endforelse
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
                        <button class="px-4 h-10 rounded-lg ring-1 ring-zinc-300 dark:ring-white/10 text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-white/10" @click="open=false">Cerrar</button>
                        <template x-if="(item?.status ?? '') !== 'cancelada'">
                            <button class="px-4 h-10 rounded-lg bg-red-600 text-white hover:bg-red-700" @click="confirmCancel=true">Cancelar</button>
                        </template>
                    </div>
                </div>
            </template>
            <template x-if="confirmCancel">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-red-600/10 text-red-600 dark:bg-red-500/15 dark:text-red-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l4-2 4 2 4-2 4 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/></svg>
                        </div>
                        <h4 class="text-lg font-semibold text-zinc-900 dark:text-white">Confirmar cancelación</h4>
                    </div>
                    <p class="mt-3 text-sm text-zinc-700 dark:text-zinc-300">¿Deseas cancelar esta reserva? Esta acción no afecta el sistema aún, pero actualizará el estado en esta vista.</p>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button class="px-4 h-10 rounded-lg ring-1 ring-zinc-300 dark:ring-white/10 text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-white/10" @click="confirmCancel=false">Volver</button>
                        <button class="px-4 h-10 rounded-lg bg-red-600 text-white hover:bg-red-700" @click="item.status='cancelada'; item.estado_label='Cancelada'; confirmCancel=false; open=false;">Confirmar</button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</section>
