<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Editar Usuario</flux:heading>
                <flux:subheading>Actualiza la información de {{ $user->name }}</flux:subheading>
            </div>
            
            <flux:button :href="route('admin.users.index')" variant="ghost" icon="arrow-left">
                Volver
            </flux:button>
        </div>

        {{-- Formulario --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Información Personal --}}
                <div>
                    <flux:heading size="lg" class="mb-4">Información Personal</flux:heading>
                    
                    <div class="grid gap-6 md:grid-cols-2">
                        {{-- Nombre --}}
                        <div>
                            <flux:input 
                                name="name" 
                                label="Nombre Completo" 
                                placeholder="Ej: Juan Pérez"
                                value="{{ old('name', $user->name) }}"
                                required
                            />
                            @error('name')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <flux:input 
                                name="email" 
                                type="email"
                                label="Correo Electrónico" 
                                placeholder="ejemplo@bgago.com"
                                value="{{ old('email', $user->email) }}"
                                required
                            />
                            @error('email')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>

                        {{-- Teléfono --}}
                        <div>
                            <flux:input 
                                name="phone" 
                                type="tel"
                                label="Teléfono" 
                                placeholder="3001234567"
                                value="{{ old('phone', $user->phone) }}"
                            />
                            @error('phone')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>

                        {{-- Rol --}}
                        <div>
                            <flux:select name="role" label="Rol" placeholder="Selecciona un rol" required>
                                @php
                                    $currentRole = old('role', $user->roles->first()?->name ?? 'cliente');
                                @endphp
                                <option value="cliente" {{ $currentRole === 'cliente' ? 'selected' : '' }}>Cliente</option>
                                <option value="conductor" {{ $currentRole === 'conductor' ? 'selected' : '' }}>Conductor</option>
                                <option value="admin" {{ $currentRole === 'admin' ? 'selected' : '' }}>Administrador</option>
                            </flux:select>
                            @error('role')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Cambiar Contraseña (Opcional) --}}
                <div>
                    <flux:heading size="lg" class="mb-2">Cambiar Contraseña</flux:heading>
                    <flux:subheading class="mb-4">Deja en blanco si no deseas cambiar la contraseña</flux:subheading>
                    
                    <div class="grid gap-6 md:grid-cols-2">
                        {{-- Nueva Contraseña --}}
                        <div>
                            <flux:input 
                                name="password" 
                                type="password"
                                label="Nueva Contraseña" 
                                placeholder="Mínimo 8 caracteres"
                            />
                            @error('password')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>

                        {{-- Confirmar Contraseña --}}
                        <div>
                            <flux:input 
                                name="password_confirmation" 
                                type="password"
                                label="Confirmar Nueva Contraseña" 
                                placeholder="Repite la contraseña"
                            />
                        </div>
                    </div>
                </div>

                {{-- Información Adicional --}}
                <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-4">
                    <div class="grid gap-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-zinc-600 dark:text-zinc-400">ID:</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-600 dark:text-zinc-400">Registrado:</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-600 dark:text-zinc-400">Última actualización:</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <flux:button 
                        :href="route('admin.users.index')" 
                        variant="ghost"
                    >
                        Cancelar
                    </flux:button>
                    
                    <flux:button 
                        type="submit" 
                        variant="primary"
                        icon="check"
                    >
                        Actualizar Usuario
                    </flux:button>
                </div>
            </form>
        </div>

    </div>
</x-layouts.app>
