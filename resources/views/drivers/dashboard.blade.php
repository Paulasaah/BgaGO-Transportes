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
$revenueToday = $reservas->filter(fn($r) => $r->estado === ReservationStatus::Completada && optional($r->fecha_fin)->isToday())->sum(fn($r) => $r->monto_final ?? 0);

$online = (bool)($driver?->driverProfile?->is_active ?? true);

if (!$assignedVehicle && $reservas->first()) {
    $assignedVehicle = Vehicle::find($reservas->first()->vehiculo_id);
}

$location = $assignedVehicle?->getCurrentLocation();
$telemetry = null;
if ($assignedVehicle) {
    $activeRes = $reservas->first(fn($r) => in_array($r->estado, [ReservationStatus::Confirmada, ReservationStatus::Activa]));
    $vel = $activeRes && $activeRes->duracion_minutos > 0
        ? max(5, round(($activeRes->distancia_km / $activeRes->duracion_minutos) * 60))
        : 42;
    $telemetry = [
        'placa' => $assignedVehicle->placa,
        'velocidad' => $vel,
        'rumbo' => 'NE',
        'lat' => $location['lat'] ?? ($activeRes->origen_lat ?? 7.119),
        'lng' => $location['lon'] ?? ($activeRes->origen_lng ?? -73.123),
        'status' => $activeRes ? 'en_ruta' : 'available',
    ];
}

// Solicitudes de domicilio pendientes del conductor (desde seeders)
$domiciliosPendientes = $reservas->filter(function($r){
    return ($r->tipo === \App\Enums\ReservationType::Domicilio) && ($r->estado === \App\Enums\ReservationStatus::Pendiente);
});
$pendingDomiciliosCount = $domiciliosPendientes->count();
$nextReservation = $reservas->filter(fn($r) => $r->estado === \App\Enums\ReservationStatus::Pendiente)->sortBy('fecha_inicio')->first();
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
            'pendingDomiciliosCount' => $pendingDomiciliosCount,
            'revenueToday' => $revenueToday,
            'assignedVehicle' => $assignedVehicle,
            'online' => $online,
        ])

        @if($nextReservation)
        <div class="grid gap-6 sm:grid-cols-1 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-gradient-to-br from-white to-blue-50 dark:from-zinc-900 dark:to-zinc-900 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Próxima reserva</p>
                            <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $nextReservation->codigo }}</p>
                            <div class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $nextReservation->origen_direccion }} → {{ $nextReservation->destino_direccion }}</div>
                            <div class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Inicio: {{ optional($nextReservation->fecha_inicio)->format('d/m H:i') }}</div>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                            <flux:icon.calendar class="h-6 w-6" />
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Ingresos hoy</p>
                            <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-zinc-100">${{ number_format($revenueToday, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                            <flux:icon.currency-dollar class="h-6 w-6" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

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

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center justify-between px-6 pt-6">
                <flux:heading size="lg">Solicitudes de domicilio</flux:heading>
                <flux:subheading>Pedidos pendientes asignados a ti</flux:subheading>
            </div>
            <div class="px-6 pb-6">
                @if($domiciliosPendientes->isEmpty())
                    <div class="py-12 text-center text-sm text-zinc-600 dark:text-zinc-400">No hay solicitudes pendientes</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-sm">
                            <thead>
                                <tr class="text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                                    <th class="px-3 py-2">Código</th>
                                    <th class="px-3 py-2">Origen</th>
                                    <th class="px-3 py-2">Destino</th>
                                    <th class="px-3 py-2">Monto</th>
                                    <th class="px-3 py-2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                @foreach($domiciliosPendientes as $r)
                                    <tr>
                                        <td class="px-3 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $r->codigo }}</td>
                                        <td class="px-3 py-3 text-zinc-700 dark:text-zinc-300">{{ $r->origen_direccion }}</td>
                                        <td class="px-3 py-3 text-zinc-700 dark:text-zinc-300">{{ $r->destino_direccion }}</td>
                                        <td class="px-3 py-3 text-zinc-700 dark:text-zinc-300">${{ number_format($r->monto_final ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-3 py-3">
                                            <div class="flex items-center gap-2">
                                                <a href="#" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold bg-blue-500 text-white hover:bg-blue-600"
                                                   @click.prevent="window.dispatchEvent(new CustomEvent('open-reserva-detalle', { detail: { id: {{ $r->id }} } }))">Ver detalle</a>
                                                <form action="{{ route('driver.reservations.accept', $r) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold bg-green-500 text-white hover:bg-green-600">Aceptar</button>
                                                </form>
                                                <form action="{{ route('driver.reservations.reject', $r) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold bg-red-500 text-white hover:bg-red-600">Rechazar</button>
                                                </form>
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
    </div>
</x-layouts.app>
