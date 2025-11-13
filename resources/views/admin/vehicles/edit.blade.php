<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Editar Vehículo</flux:heading>
                <flux:subheading>Placa {{ $vehicle->placa }}</flux:subheading>
            </div>
            <div>
                <flux:button href="{{ route('admin.vehicles.show', $vehicle) }}" variant="ghost" icon="arrow-left">Volver</flux:button>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-8 max-w-5xl mx-auto">
            <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}">
                @csrf
                @method('PUT')

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <flux:field>
                            <flux:label>Placa</flux:label>
                            <flux:input name="placa" value="{{ old('placa', $vehicle->placa) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Marca</flux:label>
                            <flux:input name="marca" value="{{ old('marca', $vehicle->marca) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Modelo</flux:label>
                            <flux:input name="modelo" value="{{ old('modelo', $vehicle->modelo) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Año</flux:label>
                            <flux:input type="number" name="year" value="{{ old('year', $vehicle->year) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Tipo</flux:label>
                            <select name="tipo" class="px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm w-full">
                                @foreach($tipos as $t)
                                    <option value="{{ $t->value }}" @selected(old('tipo', $vehicle->tipo->value) == $t->value)>{{ $t->label() }}</option>
                                @endforeach
                            </select>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Color</flux:label>
                            <flux:input name="color" value="{{ old('color', $vehicle->color) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Capacidad</flux:label>
                            <flux:input type="number" name="capacidad" value="{{ old('capacidad', $vehicle->capacidad) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Conductor asignado</flux:label>
                            <select name="conductor_id" class="px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm w-full">
                                <option value="">Sin asignar</option>
                                @foreach($conductores as $c)
                                    <option value="{{ $c->id }}" @selected(old('conductor_id', $vehicle->conductor_id) == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Sede</flux:label>
                            <select name="sede_id" class="px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm w-full">
                                <option value="">No aplica</option>
                                @foreach($sedes as $s)
                                    <option value="{{ $s->id }}" @selected(old('sede_id', $vehicle->sede_id) == $s->id)>{{ $s->nombre }}</option>
                                @endforeach
                            </select>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Estado</flux:label>
                            <select name="estado" class="px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm w-full">
                                @foreach(\App\Enums\VehicleStatus::cases() as $s)
                                    <option value="{{ $s->value }}" @selected(old('estado', $vehicle->estado->value) == $s->value)>{{ $s->label() }}</option>
                                @endforeach
                            </select>
                        </flux:field>
                    </div>
                </div>

                <div class="mt-8 grid gap-6 md:grid-cols-2">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="tiene_aire_acondicionado" id="aire" class="rounded" @checked(old('tiene_aire_acondicionado', $vehicle->tiene_aire_acondicionado))>
                        <label for="aire" class="text-sm">Tiene aire acondicionado</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="tiene_gps" id="gps" class="rounded" @checked(old('tiene_gps', $vehicle->tiene_gps))>
                        <label for="gps" class="text-sm">Tiene GPS</label>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3">
                    <flux:button href="{{ route('admin.vehicles.show', $vehicle) }}" variant="ghost">Cancelar</flux:button>
                    <flux:button type="submit" icon="check" variant="primary">Guardar cambios</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>