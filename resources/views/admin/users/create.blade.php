<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Nuevo Usuario</flux:heading>
                <flux:subheading>Crear un usuario en el sistema</flux:subheading>
            </div>
            <div class="flex items-center gap-2">
                <flux:button href="{{ route('admin.users.index') }}" variant="ghost" icon="arrow-left">
                    Volver
                </flux:button>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-8 max-w-5xl mx-auto">
            @if($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400">
                    Hay errores en el formulario. Revisa los campos marcados.
                </div>
            @endif
            <form method="POST" action="{{ route('admin.users.store') }}" class="grid gap-6">
                @csrf

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <flux:input name="name" label="Nombre" placeholder="Nombre completo" required value="{{ old('name') }}" />
                        @error('name')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <flux:input name="email" type="email" label="Correo" placeholder="email@ejemplo.com" required value="{{ old('email') }}" />
                        @error('email')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <flux:input name="phone" label="Teléfono" placeholder="Opcional" value="{{ old('phone') }}" />
                        @error('phone')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <flux:select name="role" label="Rol" placeholder="Selecciona un rol" required>
                            <option value="admin" {{ old('role')==='admin' ? 'selected' : '' }}>Admin</option>
                            <option value="conductor" {{ old('role')==='conductor' ? 'selected' : '' }}>Conductor</option>
                            <option value="cliente" {{ old('role')==='cliente' ? 'selected' : '' }}>Cliente</option>
                        </flux:select>
                        @error('role')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <flux:input name="password" type="password" label="Contraseña" required />
                        @error('password')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <flux:input name="password_confirmation" type="password" label="Confirmar contraseña" required />
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-2">
                    <flux:button href="{{ route('admin.users.index') }}" variant="outline" wire:navigate>Cancelar</flux:button>
                    <flux:button type="submit" variant="primary" icon="check">Guardar</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>