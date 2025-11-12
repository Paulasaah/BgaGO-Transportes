<x-layouts.app>
    <div class="max-w-3xl mx-auto p-6 lg:p-8 flex flex-col gap-8">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Editar Usuario</flux:heading>
                <flux:subheading>Actualiza los datos del usuario seleccionado</flux:subheading>
            </div>
            <flux:button href="{{ route('admin.users.index') }}" icon="arrow-left" variant="ghost">
                Volver
            </flux:button>
        </div>

        {{-- Formulario --}}
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:input
                    name="name"
                    label="Nombre completo"
                    value="{{ old('name', $user->name) }}"
                    required
                />
                <flux:input
                    name="email"
                    type="email"
                    label="Correo electrónico"
                    value="{{ old('email', $user->email) }}"
                    required
                />
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:input
                    name="phone"
                    label="Teléfono"
                    value="{{ old('phone', $user->phone) }}"
                />
                <flux:select
                    name="role"
                    label="Rol de usuario"
                    required
                >
                    <option value="admin" {{ $user->hasRole('admin') ? 'selected' : '' }}>Administrador</option>
                    <option value="conductor" {{ $user->hasRole('conductor') ? 'selected' : '' }}>Conductor</option>
                    <option value="cliente" {{ $user->hasRole('cliente') ? 'selected' : '' }}>Cliente</option>
                </flux:select>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:input
                    name="password"
                    type="password"
                    label="Nueva contraseña"
                    placeholder="Dejar vacío para mantener la actual"
                />
                <flux:input
                    name="password_confirmation"
                    type="password"
                    label="Confirmar nueva contraseña"
                />
            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('admin.users.index') }}" variant="ghost">
                    Cancelar
                </flux:button>
                <flux:button type="submit" variant="primary" icon="check-circle">
                    Guardar cambios
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts.app>
