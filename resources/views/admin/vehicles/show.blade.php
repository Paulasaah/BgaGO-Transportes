<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Detalle de Vehículo</flux:heading>
                <flux:subheading>Placa {{ $vehicle->placa }}</flux:subheading>
            </div>
            <div class="flex items-center gap-2">
                <flux:button href="{{ route('admin.vehicles.edit', $vehicle) }}" icon="pencil">Editar</flux:button>
                <flux:button href="{{ route('admin.vehicles.index') }}" variant="ghost" icon="arrow-left">Volver</flux:button>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="sm">Información</flux:heading>
                <div class="mt-4 text-sm">
                    <div>Marca<br><span class="font-medium">{{ $vehicle->marca }}</span></div>
                    <div class="mt-2">Modelo<br><span class="font-medium">{{ $vehicle->modelo }}</span></div>
                    <div class="mt-2">Año<br><span class="font-medium">{{ $vehicle->year }}</span></div>
                    <div class="mt-2">Tipo<br><span class="font-medium">{{ $vehicle->tipo->label() }}</span></div>
                    <div class="mt-2">Estado<br><span class="font-medium">{{ $vehicle->estado->label() }}</span></div>
                </div>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="sm">Conductor</flux:heading>
                <div class="mt-4 text-sm">
                    @if($vehicle->driver)
                        <div>Nombre<br><span class="font-medium">{{ $vehicle->driver->name }}</span></div>
                        <div class="mt-2">Email<br><span class="font-medium">{{ $vehicle->driver->email }}</span></div>
                        <div class="mt-3"><flux:button href="{{ route('admin.drivers.show', $vehicle->driver) }}" variant="ghost">Ver perfil</flux:button></div>
                    @else
                        <div class="text-zinc-500">Sin conductor asignado</div>
                    @endif
                </div>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <flux:heading size="sm">Sede</flux:heading>
                <div class="mt-4 text-sm">
                    @if($vehicle->branch)
                        <div>Nombre<br><span class="font-medium">{{ $vehicle->branch->nombre }}</span></div>
                    @else
                        <div class="text-zinc-500">Sin sede</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <flux:heading size="sm">Reservas recientes</flux:heading>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Inicio</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach($vehicle->reservations as $r)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4">{{ $r->codigo }}</td>
                                <td class="px-6 py-4">{{ $r->user?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $r->estado->label() }}</td>
                                <td class="px-6 py-4">{{ optional($r->fecha_inicio)->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <flux:button href="{{ route('admin.reservations.show', $r) }}" size="sm" variant="ghost" icon="eye" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>