@php
$currentRole = optional($user->roles->first())->name;
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Editar Usuario</flux:heading>
                <flux:subheading>{{ $user->name }}</flux:subheading>
            </div>
            <div class="flex items-center gap-2">
                <flux:button href="{{ route('admin.users.index') }}" variant="ghost" icon="arrow-left">
                    Volver
                </flux:button>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-8 max-w-5xl mx-auto">
            @if(session('error'))
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400">
                    Hay errores en el formulario. Revisa los campos marcados.
                </div>
            @endif
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="grid gap-6">
                @csrf
                @method('PUT')

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <flux:input name="name" label="Nombre" value="{{ old('name', $user->name) }}" required />
                        @error('name')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <flux:input name="email" type="email" label="Correo" value="{{ old('email', $user->email) }}" required />
                        @error('email')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <flux:input name="phone" label="Teléfono" value="{{ old('phone', $user->phone) }}" />
                        @error('phone')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <flux:select name="role" label="Rol" required>
                            @php $roleOld = old('role', $currentRole); @endphp
                            <option value="admin" {{ $roleOld === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="conductor" {{ $roleOld === 'conductor' ? 'selected' : '' }}>Conductor</option>
                            <option value="cliente" {{ $roleOld === 'cliente' ? 'selected' : '' }}>Cliente</option>
                        </flux:select>
                        @error('role')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <flux:input name="password" type="password" label="Nueva contraseña" />
                    </div>
                    <div>
                        <flux:input name="password_confirmation" type="password" label="Confirmar nueva contraseña" />
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-2">
                    <flux:button href="{{ route('admin.users.index') }}" variant="outline" wire:navigate>Cancelar</flux:button>
                    <flux:button type="submit" variant="primary" icon="check">Guardar cambios</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>