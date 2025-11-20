<x-layouts.public>
    @php
        $uid = auth()->id();
        $base = \App\Models\Reservation::with(['vehicle'])->where('user_id', $uid);
        $act = $base->clone()->whereIn('estado', ['pendiente','activa'])->orderByDesc('fecha_inicio');
        $pas = $base->clone()->whereIn('estado', ['completada','cancelada'])->orderByDesc('fecha_inicio');
        $s = trim(request('s',''));
        if ($s !== '') {
            $like = "%{$s}%";
            $act->where(function($q) use($like){ $q->where('origen_direccion','like',$like)->orWhere('destino_direccion','like',$like); });
            $pas->where(function($q) use($like){ $q->where('origen_direccion','like',$like)->orWhere('destino_direccion','like',$like); });
        }
        $activas = $act->get();
        $pasadas = $pas->get();
        $countActivas = $base->clone()->where('estado','activa')->count();
        $countPendientes = $base->clone()->where('estado','pendiente')->count();
        $countCompletadas = $base->clone()->where('estado','completada')->count();
        $thumb = function ($vehicle) {
            $v = strtolower($vehicle ?? '');
            if (str_contains($v, 'moto')) return asset('images/catalog/motocicleta.png');
            if (str_contains($v, 'bici')) return asset('images/catalog/bicicleta_electrica.png');
            if (str_contains($v, 'patineta')) return asset('images/catalog/patineta.png');
            if (str_contains($v, 'scooter')) return asset('images/catalog/scooter_electrico.png');
            return asset('images/catalog/motocicleta.png');
        };
        $estadoClass = function($s){
            return match($s){
                'pendiente' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                'activa' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                'completada' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                'cancelada' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                default => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300'
            };
        };
        $estadoLabel = function($s){ return $s==='activa' ? 'En Curso' : ucfirst($s); };
        $tipoClass = fn($t) => $t==='domicilio'
            ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'
            : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300';
        $tipoLabel = fn($t) => $t==='domicilio' ? 'Domicilio' : 'Reserva';
    @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ tab: 'activas', open:false, item:null, confirmCancel:false, hidden: {}, canceling:false, error:null }">
        <div class="relative mt-2 rounded-3xl overflow-hidden ring-1 ring-zinc-200/70 dark:ring-white/10 bg-blue-600/10 dark:bg-blue-950/20">
            <div class="absolute inset-0" style="background-image: repeating-linear-gradient(135deg, rgba(30, 64, 175, 0.15) 0px, rgba(30, 64, 175, 0.15) 40px, transparent 40px, transparent 80px);"></div>
            <div class="relative z-10 grid lg:grid-cols-2 gap-6 p-8 sm:p-10">
                <div class="flex flex-col justify-center">
                    <h1 class="text-3xl sm:text-4xl font-bold text-zinc-900 dark:text-white">Mis reservas</h1>
                    <p class="mt-3 text-zinc-700 dark:text-zinc-300">Consulta tus reservas activas y pasadas.</p>
                    <div class="mt-6 flex items-center gap-3">
                        <a href="{{ route('catalog.reserve') }}" wire:navigate class="inline-flex items-center justify-center h-11 px-5 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Reservar vehículo</a>
                        <a href="{{ route('services.delivery') }}" wire:navigate class="inline-flex items-center justify-center h-11 px-5 rounded-lg bg-white text-zinc-900 hover:bg-zinc-100 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">Solicitar domicilio</a>
                    </div>
                </div>
                <div class="h-48 sm:h-56 lg:h-64">
                    <img src="{{ asset('images/about/conductor.png') }}" alt="Domicilio" class="absolute right-6 bottom-0 z-10 w-64 sm:w-72 lg:w-80 object-contain" style="transform: scale(1.2); transform-origin: bottom right;" />
                </div>
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl bg-white dark:bg-zinc-900 ring-1 ring-zinc-200 dark:ring-zinc-800 p-4">
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">✓</div>
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Activas</div>
                        <div class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $countActivas }}</div>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-white dark:bg-zinc-900 ring-1 ring-zinc-200 dark:ring-zinc-800 p-4">
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300">!</div>
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Pendientes</div>
                        <div class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $countPendientes }}</div>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-white dark:bg-zinc-900 ring-1 ring-zinc-200 dark:ring-zinc-800 p-4">
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">✓</div>
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Completadas</div>
                        <div class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $countCompletadas }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 rounded-2xl ring-1 ring-zinc-200 dark:ring-zinc-800 bg-white dark:bg-zinc-900 p-4">
            <div class="flex items-center justify-between">
                <div class="inline-flex rounded-lg ring-1 ring-zinc-300 dark:ring-zinc-700 overflow-hidden">
                    <button @click="tab='activas'" :class="tab==='activas' ? 'bg-blue-600 text-white' : 'bg-white text-zinc-700'" class="px-4 h-9 dark:bg-zinc-900 dark:text-zinc-300">Activas</button>
                    <button @click="tab='pasadas'" :class="tab==='pasadas' ? 'bg-blue-600 text-white' : 'bg-white text-zinc-700'" class="px-4 h-9 dark:bg-zinc-900 dark:text-zinc-300">Pasadas</button>
                </div>
                <form method="GET" action="{{ route('catalog.reservations') }}" class="w-64">
                    <input type="text" name="s" value="{{ $s }}" placeholder="Buscar por origen/destino" class="w-full h-9 rounded-lg bg-zinc-50 dark:bg-zinc-900 ring-1 ring-zinc-300 dark:ring-zinc-700 px-3 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-500 dark:placeholder:text-zinc-500" />
                </form>
            </div>

            <div class="mt-4 space-y-3" x-show="tab==='activas'">
                @forelse($activas as $r)
                @php
                    $vehLabel = optional($r->vehicle)->nombre ?? optional($r->vehicle?->tipo)->label() ?? 'Vehículo';
                @endphp
                <div class="rounded-xl ring-1 ring-zinc-200 dark:ring-zinc-800 bg-white dark:bg-zinc-900 p-4 flex items-center justify-between" x-show="!hidden[$el.dataset.code]" x-transition.opacity.duration.200ms data-code="{{ $r->codigo }}">
                    <div class="flex items-center gap-3">
                        <img src="{{ $thumb($vehLabel) }}" alt="Vehículo" class="h-10 w-10 rounded-lg object-contain bg-zinc-50 dark:bg-zinc-800 p-1" />
                        <div>
                            <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $r->tipo->label() }}</div>
                            <div class="text-xs text-zinc-600 dark:text-zinc-400">{{ $r->origen_direccion }} {{ $r->destino_direccion ? '→ '.$r->destino_direccion : '' }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ optional($r->fecha_inicio)?->format('M d • h:i A') }} • COP{{ number_format((int)($r->monto_final ?? 0), 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center h-7 px-2 rounded {{ $estadoClass($r->estado->value) }} text-xs">{{ $estadoLabel($r->estado->value) }}</span>
                        <span class="inline-flex items-center h-7 px-2 rounded {{ $tipoClass($r->tipo->value) }} text-xs">{{ $tipoLabel($r->tipo->value) }}</span>
                        <button class="inline-flex items-center h-9 px-3 rounded-lg ring-1 ring-zinc-300 dark:ring-zinc-700 text-sm text-zinc-900 dark:text-white transition-all hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:shadow-md hover:-translate-y-[1px]" @click="open=true; confirmCancel=false; item=@js([
                            'tipo' => $r->tipo->label(),
                            'estado' => $estadoLabel($r->estado->value),
                            'codigo' => $r->codigo,
                            'id' => $r->id,
                            'origen' => $r->origen_direccion,
                            'destino' => $r->destino_direccion,
                            'when' => optional($r->fecha_inicio)?->format('M d • h:i A'),
                            'monto' => number_format((int)($r->monto_final ?? 0), 0, ',', '.')
                        ])">Ver detalles</button>
                    </div>
                </div>
                @empty
                @endforelse
            </div>

            <div class="mt-4 space-y-3" x-show="tab==='pasadas'" x-cloak>
                @forelse($pasadas as $r)
                @php
                    $vehLabel = optional($r->vehicle)->nombre ?? optional($r->vehicle?->tipo)->label() ?? 'Vehículo';
                @endphp
                <div class="rounded-xl ring-1 ring-zinc-200 dark:ring-zinc-800 bg-white dark:bg-zinc-900 p-4 flex items-center justify-between" x-show="!hidden[$el.dataset.code]" x-transition.opacity.duration.200ms data-code="{{ $r->codigo }}">
                    <div class="flex items-center gap-3">
                        <img src="{{ $thumb($vehLabel) }}" alt="Vehículo" class="h-10 w-10 rounded-lg object-contain bg-zinc-50 dark:bg-zinc-800 p-1" />
                        <div>
                            <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $r->tipo->label() }}</div>
                            <div class="text-xs text-zinc-600 dark:text-zinc-400">{{ $r->origen_direccion }} {{ $r->destino_direccion ? '→ '.$r->destino_direccion : '' }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ optional($r->fecha_inicio)?->format('M d • h:i A') }} • COP{{ number_format((int)($r->monto_final ?? 0), 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center h-7 px-2 rounded {{ $estadoClass($r->estado->value) }} text-xs">{{ $estadoLabel($r->estado->value) }}</span>
                        <span class="inline-flex items-center h-7 px-2 rounded {{ $tipoClass($r->tipo->value) }} text-xs">{{ $tipoLabel($r->tipo->value) }}</span>
                        <button class="inline-flex items-center h-9 px-3 rounded-lg ring-1 ring-zinc-300 dark:ring-zinc-700 text-sm text-zinc-900 dark:text-white transition-all hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:shadow-md hover:-translate-y-[1px]" @click="open=true; confirmCancel=false; item=@js([
                            'tipo' => $r->tipo->label(),
                            'estado' => $estadoLabel($r->estado->value),
                            'codigo' => $r->codigo,
                            'id' => $r->id,
                            'origen' => $r->origen_direccion,
                            'destino' => $r->destino_direccion,
                            'when' => optional($r->fecha_inicio)?->format('M d • h:i A'),
                            'monto' => number_format((int)($r->monto_final ?? 0), 0, ',', '.')
                        ])">Ver detalles</button>
                    </div>
                </div>
                @empty
                <div class="rounded-xl ring-1 ring-zinc-200 dark:ring-zinc-800 bg-white dark:bg-zinc-900 p-6 text-center text-sm text-zinc-600 dark:text-zinc-400">No hay reservas pasadas</div>
                @endforelse
            </div>
        </div>

        <div x-show="open" x-cloak class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/50" @click="open=false"></div>
            <div class="relative z-10 max-w-lg mx-auto mt-24 rounded-2xl bg-white dark:bg-zinc-900 ring-1 ring-zinc-200 dark:ring-zinc-800 p-6">
                <template x-if="!confirmCancel">
                    <div>
                        <div class="flex items-center justify-between">
                            <div class="text-lg font-semibold text-zinc-900 dark:text-white">Detalles de la reserva</div>
                        </div>
                        <div class="mt-4 space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
                            <div><span class="font-medium text-zinc-900 dark:text-white">Tipo:</span> <span x-text="item?.tipo"></span></div>
                            <div><span class="font-medium text-zinc-900 dark:text-white">Estado:</span> <span x-text="item?.estado"></span></div>
                            <div><span class="font-medium text-zinc-900 dark:text-white">Código:</span> <span x-text="item?.codigo"></span></div>
                            <div><span class="font-medium text-zinc-900 dark:text-white">Origen:</span> <span x-text="item?.origen"></span></div>
                            <div><span class="font-medium text-zinc-900 dark:text-white">Destino:</span> <span x-text="item?.destino"></span></div>
                            <div><span class="font-medium text-zinc-900 dark:text-white">Fecha:</span> <span x-text="item?.when"></span></div>
                            <div><span class="font-medium text-zinc-900 dark:text-white">Monto:</span> COP<span x-text="item?.monto"></span></div>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-3">
                            <button class="px-4 h-9 rounded-lg ring-1 ring-zinc-300 dark:ring-zinc-700 text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800" @click="open=false">Cerrar</button>
                            <template x-if="item && (item.estado==='Pendiente' || item.estado==='En Curso')">
                                <button class="px-4 h-9 rounded-lg bg-red-600 text-white hover:bg-red-700" @click="confirmCancel=true">Cancelar</button>
                            </template>
                        </div>
                    </div>
                </template>
                <template x-if="confirmCancel">
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">!</div>
                            <div class="text-lg font-semibold text-zinc-900 dark:text-white">Confirmar cancelación</div>
                        </div>
                        <p class="mt-3 text-sm text-zinc-700 dark:text-zinc-300">¿Deseas cancelar esta reserva? Esta acción actualizará el estado en esta vista.</p>
                        <template x-if="error">
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400" x-text="error"></p>
                        </template>
                        <div class="mt-6 flex items-center justify-end gap-3">
                            <button class="px-4 h-9 rounded-lg ring-1 ring-zinc-300 dark:ring-zinc-700 text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800" @click="confirmCancel=false">Volver</button>
                            <button class="px-4 h-9 rounded-lg bg-red-600 text-white hover:bg-red-700 disabled:bg-red-400" :disabled="canceling" @click="
                                (async () => {
                                    try {
                                        canceling = true; error = null;
                                        await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
                                        const res = await fetch(`/catalog/reservations/${item.id}/cancel`, {
                                            method: 'POST',
                                            credentials: 'same-origin',
                                            headers: {
                                                'Accept': 'application/json',
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
                                                'X-XSRF-TOKEN': decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || '')
                                            },
                                            body: JSON.stringify({ motivo_cancelacion: 'Cancelada por el usuario' })
                                        });
                                        if (res.ok) {
                                            hidden[item.codigo] = true;
                                            confirmCancel = false; open = false;
                                        } else {
                                            const d = await res.json().catch(() => ({}));
                                            error = d.message || 'No se pudo cancelar';
                                        }
                                    } catch (e) {
                                        error = 'Error de red';
                                    } finally {
                                        canceling = false;
                                    }
                                })();
                            ">Confirmar</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</x-layouts.public>
