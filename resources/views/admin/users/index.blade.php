<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Gestión de Usuarios</flux:heading>
                <flux:subheading>Administra los usuarios registrados en BgaGO</flux:subheading>
            </div>
            <flux:button href="{{ route('admin.users.create') }}" icon="plus" variant="primary">
                Nuevo Usuario
            </flux:button>
        </div>

        {{-- Tarjetas de estadísticas --}}
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <x-stats.card title="Total Usuarios" :value="$stats['total']" icon="users" color="blue" />
            <x-stats.card title="Clientes" :value="$stats['clientes']" icon="user-group" color="green" />
            <x-stats.card title="Conductores" :value="$stats['conductores']" icon="user-circle" color="orange" />
            <x-stats.card title="Administradores" :value="$stats['admins']" icon="shield-check" color="purple" />
        </div>

        {{-- Filtros --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-4">
            <form method="GET" class="flex flex-wrap gap-3">
                <flux:input 
                    name="search" 
                    placeholder="Buscar por nombre o email..."
                    value="{{ request('search') }}"
                    class="flex-1 min-w-[200px]"
                />
                
                <flux:select name="role" placeholder="Filtrar por rol" class="min-w-[140px]">
                    <option value="">Todos los roles</option>
                    <option value="cliente" {{ request('role') === 'cliente' ? 'selected' : '' }}>Cliente</option>
                    <option value="conductor" {{ request('role') === 'conductor' ? 'selected' : '' }}>Conductor</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </flux:select>

                <flux:button type="submit" icon="magnifying-glass">
                    Buscar
                </flux:button>

                @if(request()->hasAny(['search', 'role']))
                    <flux:button href="{{ url()->current() }}" variant="ghost" icon="x-circle">
                        Limpiar filtros
                    </flux:button>
                @endif
            </form>
        </div>

        {{-- Tabla --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            @if($users->isEmpty())
                <div class="text-center py-12 text-zinc-500 dark:text-zinc-400">
                    <flux:icon.users class="size-12 mx-auto mb-3 opacity-50" />
                    <p class="text-lg font-medium">No hay usuarios registrados</p>
                    <p class="text-sm mt-2">Comienza agregando un nuevo usuario</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Correo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Roles</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Registro</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                            @foreach($users as $user)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">ID: {{ $user->id }}</div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-zinc-900 dark:text-zinc-100">{{ $user->email }}</div>
                                        @if($user->phone)
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->phone }}</div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @forelse($user->roles as $role)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium
                                                @class([
                                                    'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' => $role->name === 'admin',
                                                    'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400' => $role->name === 'conductor',
                                                    'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' => $role->name === 'cliente',
                                                ])
                                            ">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @empty
                                            <span class="text-zinc-400 dark:text-zinc-500 text-xs italic">Sin rol</span>
                                        @endforelse
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $user->created_at->format('d/m/Y') }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <flux:button size="sm" variant="ghost" icon="eye" href="{{ route('admin.users.show', $user) }}" title="Ver" class="text-zinc-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400" />
                                            <flux:button size="sm" variant="ghost" icon="pencil" href="{{ route('admin.users.edit', $user) }}" title="Editar" class="text-zinc-600 dark:text-zinc-400 hover:text-green-600 dark:hover:text-green-400" />
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <flux:button size="sm" variant="ghost" icon="trash" title="Eliminar" class="text-zinc-600 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400" />
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
