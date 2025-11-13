@php
    $res = $reservation;
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Detalle de Reserva</flux:heading>
                <flux:subheading>Código: {{ $res->codigo }}</flux:subheading>
            </div>
            <div class="flex items-center gap-2">
                <flux:button href="{{ route('admin.reservations.edit', $res) }}" icon="pencil">Editar</flux:button>
                <flux:button href="{{ route('admin.reservations.index') }}" variant="ghost" icon="arrow-left">Volver</flux:button>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="sm">Cliente</flux:heading>
                <div class="mt-4 text-sm">
                    <div>Nombre<br><span class="font-medium">{{ $res->user->name ?? 'N/A' }}</span></div>
                    <div class="mt-2">Email<br><span class="font-medium">{{ $res->user->email ?? 'N/A' }}</span></div>
                </div>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="sm">Vehículo</flux:heading>
                <div class="mt-4 text-sm">
                    <div>Placa<br><span class="font-mono font-medium">{{ $res->vehicle?->placa ?? 'Sin asignar' }}</span></div>
                    <div class="mt-2">Vehículo<br><span class="font-medium">{{ $res->vehicle ? ($res->vehicle->marca . ' ' . $res->vehicle->modelo) : 'N/A' }}</span></div>
                    @if($res->vehicle)
                        <div class="mt-3"><flux:button href="{{ route('admin.vehicles.show', $res->vehicle) }}" variant="ghost">Ver detalles del vehículo</flux:button></div>
                    @endif
                </div>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="sm">Conductor</flux:heading>
                <div class="mt-4 text-sm">
                    <div>Nombre<br><span class="font-medium">{{ $res->driver?->name ?? 'N/A' }}</span></div>
                    <div class="mt-2">Email<br><span class="font-medium">{{ $res->driver?->email ?? 'N/A' }}</span></div>
                    @if($res->driver)
                        <div class="mt-3"><flux:button href="{{ route('admin.drivers.show', $res->driver) }}" variant="ghost">Ver perfil</flux:button></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="sm">Fechas y Horarios</flux:heading>
                <div class="mt-4 text-sm">
                    <div>Inicio<br><span class="font-medium">{{ optional($res->fecha_inicio)->format('d/m/Y H:i') }}</span></div>
                    <div class="mt-2">Fin<br><span class="font-medium">{{ optional($res->fecha_fin)->format('d/m/Y H:i') }}</span></div>
                    <div class="mt-2">Duración<br><span class="font-medium">{{ $res->duracion_minutos ? floor($res->duracion_minutos/60).'h' : '—' }}</span></div>
                </div>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="sm">Ubicación</flux:heading>
                <div class="mt-4 text-sm">
                    <div>Sede<br><span class="font-medium">{{ $res->branch?->nombre ?? 'N/A' }}</span></div>
                    <div class="mt-2">Origen<br><span class="font-medium">{{ $res->origen_direccion ?? 'N/A' }}</span></div>
                    <div class="mt-2">Destino<br><span class="font-medium">{{ $res->destino_direccion ?? 'N/A' }}</span></div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <flux:heading size="sm">Información de Pago</flux:heading>
            <div class="mt-4 text-sm">
                <div>Subtotal<br><span class="font-medium">${{ number_format($res->monto ?? 0, 0, ',', '.') }}</span></div>
                <div class="mt-2">Descuento<br><span class="font-medium">-${{ number_format($res->descuento ?? 0, 0, ',', '.') }}</span></div>
                <div class="mt-2">Total Final<br><span class="font-medium text-green-600 dark:text-green-400">${{ number_format($res->monto_final ?? 0, 0, ',', '.') }}</span></div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <flux:heading size="sm">Notas</flux:heading>
            <div class="mt-4 text-sm">
                <div>Notas del Cliente<br><span class="font-medium">{{ $res->notas_cliente ?? '—' }}</span></div>
                <div class="mt-2">Notas del Conductor<br><span class="font-medium">{{ $res->notas_conductor ?? '—' }}</span></div>
                <div class="mt-2">Notas del Admin<br><span class="font-medium">{{ $res->notas_admin ?? '—' }}</span></div>
            </div>
        </div>
    </div>
</x-layouts.app>