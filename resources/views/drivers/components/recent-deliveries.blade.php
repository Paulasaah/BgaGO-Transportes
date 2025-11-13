@php
use App\Enums\ReservationStatus;
$reservasCollection = collect($reservas);
$completadas = $reservasCollection->filter(function($r) {
    if (is_array($r)) {
        return ($r['status'] ?? '') === 'completada';
    }
    return $r->estado === ReservationStatus::Completada;
})->take(5);
@endphp

<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800">
    <div class="flex items-center justify-between px-6 pt-6">
        <flux:heading size="lg">Entregas recientes</flux:heading>
    </div>
    <div class="px-6 pb-6">
        @if($completadas->isEmpty())
            <div class="py-12 text-center text-sm text-zinc-600 dark:text-zinc-400">
                No hay entregas recientes
            </div>
        @else
            <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @foreach($completadas as $r)
                    <div class="flex items-center justify-between py-4">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-lg bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 flex items-center justify-center">
                                <i data-lucide="check-circle-2" class="h-5 w-5"></i>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ is_array($r) ? ($r['codigo'] ?? '') : ($r->codigo ?? '') }}</div>
                                <div class="text-xs text-zinc-600 dark:text-zinc-400">{{ is_array($r) ? ($r['origen'] ?? '') : ($r->origen_direccion ?? '') }} → {{ is_array($r) ? ($r['destino'] ?? '') : ($r->destino_direccion ?? '') }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Completado</span>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">${{ is_array($r) ? number_format(($r['monto'] ?? 0), 0, ',', '.') : number_format(($r->monto_final ?? 0), 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
