@php
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Reservation;
use App\Enums\ReservationStatus;

$driver = User::role('conductor')->first();
$driverName = $driver?->name ?? 'Conductor';
$assignedVehicle = $driver ? Vehicle::where('conductor_id', $driver->id)->first() : null;
$reservas = $driver ? Reservation::delConductor($driver->id)->with('vehicle')->orderByDesc('fecha_inicio')->get() : collect();

$activeCount = $reservas->filter(fn($r) => $r->estado === ReservationStatus::Activa || $r->estado === ReservationStatus::Confirmada)->count();
$completedToday = $reservas->filter(fn($r) => $r->estado === ReservationStatus::Completada && optional($r->fecha_fin)->isToday())->count();

$online = (bool)($driver?->driverProfile?->is_active ?? true);

$location = $assignedVehicle?->getCurrentLocation();
$telemetry = $assignedVehicle ? [
    'placa' => $assignedVehicle->placa,
    'velocidad' => 42,
    'rumbo' => 'NE',
    'lat' => $location['lat'] ?? 7.119,
    'lng' => $location['lon'] ?? -73.123,
    'status' => 'en_ruta',
] : null;
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Hola {{ $driverName }}</flux:heading>
                <flux:subheading>Dashboard del Conductor</flux:subheading>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $online ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-300' }}">Conductor Activo</span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">{{ is_array($assignedVehicle) ? ($assignedVehicle['placa'] ?? '') : ($assignedVehicle?->placa ?? '') }}</span>
            </div>
        </div>

        @include('drivers.components.stats', [
            'activeCount' => $activeCount,
            'completedToday' => $completedToday,
            'assignedVehicle' => $assignedVehicle,
            'online' => $online,
        ])

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                @include('drivers.components.active-reservations', [
                    'reservas' => $reservas,
                ])
            </div>
            <div>
                @include('drivers.components.telemetry-status', [
                    'telemetry' => $telemetry,
                    'assignedVehicle' => $assignedVehicle,
                ])
            </div>
        </div>

        @include('drivers.components.recent-deliveries', [
            'reservas' => $reservas,
        ])
    </div>
</x-layouts.app>
