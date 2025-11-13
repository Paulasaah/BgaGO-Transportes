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
                                    <div class="flex items-center gap-2">
                                        <a href="#" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold bg-blue-500 text-white hover:bg-blue-600">Ver detalle</a>
                                        <button type="button" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold bg-green-500 text-white hover:bg-green-600">Finalizar</button>
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
