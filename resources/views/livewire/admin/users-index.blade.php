<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Gestión de Usuarios</flux:heading>
                <flux:subheading>Administra los usuarios registrados en BgaGo</flux:subheading>
            </div>
            <div class="flex items-center gap-2">
                <flux:button href="{{ route('admin.users.create') }}" icon="plus" variant="primary" wire:navigate>
                    Nuevo usuario
                </flux:button>
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <x-stats.card title="Total Usuarios" :value="$stats['total']" icon="users" color="blue" />
            <x-stats.card title="Clientes" :value="$stats['clientes']" icon="user-group" color="green" />
            <x-stats.card title="Conductores" :value="$stats['conductores']" icon="user-circle" color="orange" />
            <x-stats.card title="Administradores" :value="$stats['admins']" icon="shield-check" color="purple" />
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-4">
            <form wire:submit.prevent class="flex flex-wrap gap-3">
                <flux:input name="search" placeholder="Buscar por nombre o email..." class="flex-1 min-w-[200px]" wire:model.live="search" />
                <flux:select name="role" placeholder="Filtrar por rol" class="min-w-[140px]" wire:model.live="role">
                    <option value="">Todos los roles</option>
                    <option value="cliente">Cliente</option>
                    <option value="conductor">Conductor</option>
                    <option value="admin">Admin</option>
                </flux:select>
                <flux:button type="button" icon="magnifying-glass">Buscar</flux:button>
                <flux:button type="button" variant="ghost" wire:click="$set('search',''); $set('role','')">Limpiar</flux:button>
            </form>
        </div>

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
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Registro</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                            @foreach($users as $user)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                                <span class="text-white font-semibold text-sm">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">{{ $user->email }}</div>
                                    @if($user->phone)
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->phone }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->roles->isNotEmpty())
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium @if($user->hasRole('admin')) bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 @elseif($user->hasRole('conductor')) bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 @else bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 @endif">
                                            {{ ucfirst($user->roles->first()->name) }}
                                        </span>
                                    @else
                                        <span class="text-zinc-400 dark:text-zinc-500 text-xs italic">Sin rol</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button size="sm" variant="ghost" icon="eye" title="Ver detalles" href="{{ route('admin.users.show', $user) }}" wire:navigate class="text-zinc-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400" />
                                        <flux:button size="sm" variant="ghost" icon="pencil" title="Editar" href="{{ route('admin.users.edit', $user) }}" wire:navigate class="text-zinc-600 dark:text-zinc-400 hover:text-green-600 dark:hover:text-green-400" />
                                        <flux:button size="sm" variant="danger" icon="trash" title="Eliminar" x-data x-on:click.prevent="confirm('¿Estás seguro de eliminar este usuario?') && $wire.deleteNow({{ $user->id }})" class="text-zinc-600 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400">Eliminar</flux:button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>