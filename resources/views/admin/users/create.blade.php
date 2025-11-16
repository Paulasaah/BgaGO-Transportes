<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Crear Usuario</flux:heading>
                <flux:subheading>Registra un nuevo usuario en el sistema</flux:subheading>
            </div>
            
            <flux:button :href="route('admin.users.index')" variant="ghost" icon="arrow-left">
                Volver
            </flux:button>
        </div>

        {{-- Formulario --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                @csrf

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
                                value="{{ old('name') }}"
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
                                value="{{ old('email') }}"
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
                                value="{{ old('phone') }}"
                            />
                            @error('phone')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>

                        {{-- Rol --}}
                        <div>
                            <flux:select name="role" label="Rol" placeholder="Selecciona un rol" required>
                                <option value="cliente" {{ old('role') === 'cliente' ? 'selected' : '' }}>Cliente</option>
                                <option value="conductor" {{ old('role') === 'conductor' ? 'selected' : '' }}>Conductor</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                            </flux:select>
                            @error('role')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Seguridad --}}
                <div>
                    <flux:heading size="lg" class="mb-4">Seguridad</flux:heading>
                    
                    <div class="grid gap-6 md:grid-cols-2">
                        {{-- Contraseña --}}
                        <div>
                            <flux:input 
                                name="password" 
                                type="password"
                                label="Contraseña" 
                                placeholder="Mínimo 8 caracteres"
                                required
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
                                label="Confirmar Contraseña" 
                                placeholder="Repite la contraseña"
                                required
                            />
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
                        Crear Usuario
                    </flux:button>
                </div>
            </form>
        </div>

    </div>
</x-layouts.app>
