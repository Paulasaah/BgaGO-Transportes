<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Editar Reserva</flux:heading>
                <flux:subheading>Código: {{ $reservation->codigo }}</flux:subheading>
            </div>
            <div>
                <flux:button href="{{ route('admin.reservations.show', $reservation) }}" variant="ghost" icon="arrow-left">Volver</flux:button>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-8 max-w-5xl mx-auto">
            <form method="POST" action="{{ route('admin.reservations.update', $reservation) }}">
                @csrf
                @method('PUT')

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <flux:field>
                            <flux:label>Cliente</flux:label>
                            <select name="user_id" class="px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm w-full">
                                @foreach($clientes as $c)
                                    <option value="{{ $c->id }}" @selected(old('user_id', $reservation->user_id) == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Conductor</flux:label>
                            <select name="conductor_id" class="px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm w-full">
                                <option value="">Sin asignar</option>
                                @foreach($conductores as $d)
                                    <option value="{{ $d->id }}" @selected(old('conductor_id', $reservation->conductor_id) == $d->id)>{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Vehículo</flux:label>
                            <select name="vehiculo_id" class="px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm w-full">
                                <option value="">Sin asignar</option>
                                @foreach($vehiculos as $v)
                                    <option value="{{ $v->id }}" @selected(old('vehiculo_id', $reservation->vehiculo_id) == $v->id)>{{ $v->placa }} - {{ $v->marca }} {{ $v->modelo }}</option>
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
                                    <option value="{{ $s->id }}" @selected(old('sede_id', $reservation->sede_id) == $s->id)>{{ $s->nombre }}</option>
                                @endforeach
                            </select>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Tipo</flux:label>
                            <select name="tipo" class="px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm w-full">
                                @foreach($tipos as $t)
                                    <option value="{{ $t->value }}" @selected(old('tipo', $reservation->tipo->value) == $t->value)>{{ $t->label() }}</option>
                                @endforeach
                            </select>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Estado</flux:label>
                            <select name="estado" class="px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm w-full">
                                @foreach(\App\Enums\ReservationStatus::cases() as $s)
                                    <option value="{{ $s->value }}" @selected(old('estado', $reservation->estado->value) == $s->value)>{{ $s->label() }}</option>
                                @endforeach
                            </select>
                        </flux:field>
                    </div>
                </div>

                <div class="mt-8 grid gap-6 md:grid-cols-2">
                    <div>
                        <flux:field>
                            <flux:label>Fecha inicio</flux:label>
                            <flux:input type="datetime-local" name="fecha_inicio" value="{{ old('fecha_inicio', optional($reservation->fecha_inicio)->format('Y-m-d\TH:i')) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Fecha fin</flux:label>
                            <flux:input type="datetime-local" name="fecha_fin" value="{{ old('fecha_fin', optional($reservation->fecha_fin)->format('Y-m-d\TH:i')) }}" />
                        </flux:field>
                    </div>
                </div>

                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    <div>
                        <flux:field>
                            <flux:label>Monto</flux:label>
                            <flux:input type="number" step="0.01" name="monto" value="{{ old('monto', $reservation->monto) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Descuento</flux:label>
                            <flux:input type="number" step="0.01" name="descuento" value="{{ old('descuento', $reservation->descuento) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Total Final</flux:label>
                            <flux:input type="number" step="0.01" name="monto_final" value="{{ old('monto_final', $reservation->monto_final) }}" />
                        </flux:field>
                    </div>
                </div>

                <div class="mt-8">
                    <flux:field>
                        <flux:label>Notas de administración</flux:label>
                        <flux:textarea name="notas_admin">{{ old('notas_admin', $reservation->notas_admin) }}</flux:textarea>
                    </flux:field>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3">
                    <flux:button href="{{ route('admin.reservations.show', $reservation) }}" variant="ghost">Cancelar</flux:button>
                    <flux:button type="submit" icon="check" variant="primary">Guardar cambios</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>