@php
    $statusColor = function (string $status): string {
        $colors = [
            'active' => 'green',
            'inactive' => 'red',
            'available' => 'green',
            'busy' => 'yellow',
            'maintenance' => 'orange',
            'pending' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            'en_ruta' => 'blue',
            'confirmada' => 'green',
        ];
        $c = $colors[$status] ?? 'gray';
        return match ($c) {
            'green' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
            'yellow' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
            'orange' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
            'red' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
            'blue' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
            default => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-300',
        };
    };
    $statusLabel = function (string $status): string {
        $labels = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'available' => 'Disponible',
            'busy' => 'Ocupado',
            'maintenance' => 'Mantenimiento',
            'pending' => 'Pendiente',
            'completed' => 'Completado',
            'cancelled' => 'Cancelado',
            'en_ruta' => 'En Ruta',
            'confirmada' => 'Confirmada',
        ];
        return $labels[$status] ?? ucfirst($status);
    };
@endphp

<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800">
    <div class="flex items-center justify-between px-6 pt-6">
        <flux:heading size="lg">Telemetría</flux:heading>
    </div>
    <div class="px-6 pb-6">
        @if(!$assignedVehicle)
            <div class="py-12 text-center text-sm text-zinc-600 dark:text-zinc-400">Sin vehículo asignado</div>
        @elseif(!$telemetry)
            <div class="py-12 text-center text-sm text-zinc-600 dark:text-zinc-400">Sin datos de telemetría</div>
        @else
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">Vehículo</div>
                    <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ is_array($assignedVehicle) ? ($assignedVehicle['placa'] ?? '') : ($assignedVehicle?->placa ?? '') }}</div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">Velocidad</div>
                    <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $telemetry['velocidad'] }} km/h</div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">Rumbo</div>
                    <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $telemetry['rumbo'] ?? 'N/A' }}</div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">Ubicación</div>
                    <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $telemetry['lat'] }}, {{ $telemetry['lng'] }}</div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">Estado</div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor($telemetry['status']) }}">
                        {{ $statusLabel($telemetry['status']) }}
                    </span>
                </div>
            </div>
        @endif
    </div>
</div>