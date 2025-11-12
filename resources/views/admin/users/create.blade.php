<x-layouts.app>
    <div class="max-w-3xl mx-auto p-6 lg:p-8 flex flex-col gap-8">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Nuevo Usuario</flux:heading>
                <flux:subheading>Registra un nuevo usuario en el sistema</flux:subheading>
            </div>
            <flux:button href="{{ route('admin.users.index') }}" icon="arrow-left" variant="ghost">
                Volver
            </flux:button>
        </div>

        {{-- Formulario --}}
        <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
            @csrf

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:input
                    name="name"
                    label="Nombre completo"
                    placeholder="Ej. Juan Pérez"
                    value="{{ old('name') }}"
                    required
                />
                <flux:input
                    name="email"
                    type="email"
                    label="Correo electrónico"
                    placeholder="usuario@ejemplo.com"
                    value="{{ old('email') }}"
                    required
                />
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:input
                    name="phone"
                    label="Teléfono"
                    placeholder="Ej. 3001234567"
                    value="{{ old('phone') }}"
                />
                <flux:select
                    name="role"
                    label="Rol de usuario"
                    required
                >
                    <option value="">Seleccionar rol</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                    <option value="conductor" {{ old('role') === 'conductor' ? 'selected' : '' }}>Conductor</option>
                    <option value="cliente" {{ old('role') === 'cliente' ? 'selected' : '' }}>Cliente</option>
                </flux:select>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:input
                    name="password"
                    type="password"
                    label="Contraseña"
                    placeholder="Mínimo 8 caracteres"
                    required
                />
                <flux:input
                    name="password_confirmation"
                    type="password"
                    label="Confirmar contraseña"
                    required
                />
            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('admin.users.index') }}" variant="ghost">
                    Cancelar
                </flux:button>
                <flux:button type="submit" variant="primary" icon="check-circle">
                    Crear usuario
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts.app>
