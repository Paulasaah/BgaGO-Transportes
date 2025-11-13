@php
    $profile = $user->driverProfile;
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Editar Conductor</flux:heading>
                <flux:subheading>{{ $user->name }}</flux:subheading>
            </div>
            <div>
                <flux:button href="{{ route('admin.drivers.show', $user) }}" variant="ghost" icon="arrow-left">Volver</flux:button>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-8 max-w-5xl mx-auto">
            <form method="POST" action="{{ route('admin.drivers.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <flux:field>
                            <flux:label>Nombre completo</flux:label>
                            <flux:input name="name" value="{{ old('name', $user->name) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Correo electrónico</flux:label>
                            <flux:input type="email" name="email" value="{{ old('email', $user->email) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Teléfono</flux:label>
                            <flux:input name="phone" value="{{ old('phone', $user->phone) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Nueva contraseña</flux:label>
                            <flux:input type="password" name="password" />
                        </flux:field>
                        <flux:field class="mt-2">
                            <flux:label>Confirmar contraseña</flux:label>
                            <flux:input type="password" name="password_confirmation" />
                        </flux:field>
                    </div>
                </div>

                <div class="mt-8 grid gap-6 md:grid-cols-2">
                    <div>
                        <flux:field>
                            <flux:label>Número de licencia</flux:label>
                            <flux:input name="license_number" value="{{ old('license_number', $profile?->license_number) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Fecha de vencimiento</flux:label>
                            <flux:input type="date" name="license_expiry" value="{{ old('license_expiry', optional($profile?->license_expiry)->format('Y-m-d')) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Estado</flux:label>
                            <select name="is_active" class="px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm w-full">
                                <option value="1" @selected(old('is_active', $profile?->is_active ?? false))>Activo</option>
                                <option value="0" @selected(!old('is_active', $profile?->is_active ?? false))>Inactivo</option>
                            </select>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Vehículo asignado</flux:label>
                            <select name="assigned_vehicle_id" class="px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm w-full">
                                <option value="">Sin asignar</option>
                                @foreach($vehicles as $veh)
                                    <option value="{{ $veh->id }}" @selected(old('assigned_vehicle_id', optional($veh)->conductor_id === $user->id))>
                                        {{ $veh->placa }} - {{ $veh->marca }} {{ $veh->modelo }}
                                    </option>
                                @endforeach
                            </select>
                        </flux:field>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3">
                    <flux:button href="{{ route('admin.drivers.show', $user) }}" variant="ghost">Cancelar</flux:button>
                    <flux:button type="submit" icon="check" variant="primary">Guardar cambios</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>