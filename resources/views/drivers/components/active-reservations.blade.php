@php
use App\Enums\ReservationStatus;
$reservasCollection = collect($reservas);
$activas = $reservasCollection->filter(function($r) {
    if (is_array($r)) {
        return in_array($r['status'], ['confirmada', 'active', 'activa']);
    }
    return ($r->estado === ReservationStatus::Confirmada) || ($r->estado === ReservationStatus::Activa);
});
$statusLabel = function ($r): string {
    if (is_array($r)) {
        $labels = [
            'confirmada' => 'Confirmada',
            'active' => 'Activa',
            'activa' => 'Activa',
            'completada' => 'Completada',
            'pendiente' => 'Pendiente',
            'cancelada' => 'Cancelada',
            'en_ruta' => 'En Ruta',
        ];
        return $labels[$r['status']] ?? ucfirst($r['status']);
    }
    return $r->estado->label();
};
$statusBadgeClass = function ($r): string {
    $color = is_array($r) ? 'green' : $r->estado->color();
    return match ($color) {
        'green' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        'blue' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        'yellow' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
        'gray' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-300',
        'red' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        default => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-300',
    };
};
@endphp

<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800">
    <div class="flex items-center justify-between px-6 pt-6">
        <flux:heading size="lg">Reservas activas</flux:heading>
    </div>
    <div class="px-6 pb-6">
        @if($activas->isEmpty())
            <div class="py-12 text-center text-sm text-zinc-600 dark:text-zinc-400">
                No hay reservas activas actualmente
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                            <th class="px-3 py-2">Código</th>
                            <th class="px-3 py-2">Vehículo</th>
                            <th class="px-3 py-2">Origen</th>
                            <th class="px-3 py-2">Destino</th>
                            <th class="px-3 py-2">Estado</th>
                            <th class="px-3 py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-sm">
                        @foreach($activas as $r)
                            <tr>
                                <td class="px-3 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ is_array($r) ? ($r['codigo'] ?? '') : ($r->codigo ?? '') }}</td>
                                <td class="px-3 py-3 text-zinc-700 dark:text-zinc-300">{{ is_array($r) ? ($r['vehiculo'] ?? '') : ($r->vehicle?->placa ?? '') }}</td>
                                <td class="px-3 py-3 text-zinc-700 dark:text-zinc-300">{{ is_array($r) ? ($r['origen'] ?? '') : ($r->origen_direccion ?? '') }}</td>
                                <td class="px-3 py-3 text-zinc-700 dark:text-zinc-300">{{ is_array($r) ? ($r['destino'] ?? '') : ($r->destino_direccion ?? '') }}</td>
                                <td class="px-3 py-3">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full font-medium {{ $statusBadgeClass($r) }}">
                                        @php
                                            $isActive = is_array($r) ? in_array(($r['status'] ?? ''), ['active', 'confirmada', 'activa']) : $r->estado->isActive();
                                        @endphp
                                        @if($isActive)
                                            <span class="size-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                        @endif
                                        {{ is_array($r) ? $statusLabel($r) : $statusLabel($r) }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <div x-data="{ open: false, item: null }" class="flex items-center gap-2">
                                        <a href="#" @click.prevent="open=true; item={
                                            codigo: '{{ is_array($r) ? ($r['codigo'] ?? '') : ($r->codigo ?? '') }}',
                                            placa: '{{ is_array($r) ? ($r['vehiculo'] ?? '') : ($r->vehicle?->placa ?? '') }}',
                                            origen: '{{ is_array($r) ? ($r['origen'] ?? '') : ($r->origen_direccion ?? '') }}',
                                            destino: '{{ is_array($r) ? ($r['destino'] ?? '') : ($r->destino_direccion ?? '') }}',
                                            distancia: '{{ is_array($r) ? ($r['distancia_km'] ?? '') : ($r->distancia_km ?? '') }}',
                                            duracion: '{{ is_array($r) ? ($r['duracion_minutos'] ?? '') : ($r->duracion_minutos ?? '') }}',
                                            monto: '{{ is_array($r) ? ($r['monto_final'] ?? 0) : ($r->monto_final ?? 0) }}',
                                            notas: '{{ is_array($r) ? ($r['notas_cliente'] ?? '') : ($r->notas_cliente ?? '') }}',
                                            estado: '{{ is_array($r) ? ($r['status'] ?? '') : ($r->estado->value ?? '') }}',
                                            tipo: '{{ is_array($r) ? ($r['tipo'] ?? '') : ($r->tipo ?? '') }}'
                                        }" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold bg-blue-500 text-white hover:bg-blue-600">Ver detalle</a>
                                        @php $rid = is_array($r) ? ($r['id'] ?? null) : ($r->id ?? null); @endphp
                                        @if($rid)
                                            <form action="{{ route('driver.reservations.complete', $rid) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold bg-green-500 text-white hover:bg-green-600">Finalizar</button>
                                            </form>
                                        @endif

                                        <!-- Modal Detalle -->
                                        <div x-cloak x-show="open" x-transition.opacity.duration.150ms class="fixed inset-0 z-[95] bg-black/40"></div>
                                        <div x-cloak x-show="open" x-transition.duration.150ms class="fixed inset-0 z-[96] flex items-center justify-center p-4">
                                            <div class="w-full max-w-lg rounded-xl border border-zinc-200 bg-white shadow-lg dark:border-zinc-800 dark:bg-zinc-900">
                                                <div class="px-5 pt-5 flex items-center justify-between">
                                                    <flux:heading size="lg">Detalle de Reserva</flux:heading>
                                                    <button class="opacity-70 hover:opacity-100" @click="open=false"><flux:icon.x-mark class="size-5"/></button>
                                                </div>
                                                <div class="px-5 pt-4 pb-5 space-y-3 text-sm">
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div><span class="font-medium">Código:</span> <span x-text="item?.codigo"></span></div>
                                                        <div><span class="font-medium">Vehículo:</span> <span x-text="item?.placa"></span></div>
                                                        <div class="col-span-2"><span class="font-medium">Origen:</span> <span x-text="item?.origen"></span></div>
                                                        <div class="col-span-2"><span class="font-medium">Destino:</span> <span x-text="item?.destino"></span></div>
                                                        <div><span class="font-medium">Distancia:</span> <span x-text="item?.distancia"></span> km</div>
                                                        <div><span class="font-medium">Duración:</span> <span x-text="item?.duracion"></span> min</div>
                                                        <div><span class="font-medium">Monto:</span> $<span x-text="Number(item?.monto).toLocaleString()"></span></div>
                                                        <div><span class="font-medium">Estado:</span> <span x-text="item?.estado"></span></div>
                                                        <div><span class="font-medium">Tipo:</span> <span x-text="item?.tipo"></span></div>
                                                    </div>
                                                    <div><span class="font-medium">Notas:</span> <span x-text="item?.notas || '—'"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>