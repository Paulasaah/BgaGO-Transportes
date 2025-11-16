@php
    $reservasCount = $user->reservations()->count();
    $reservasActivas = $user->reservations()->whereIn('estado', ['pendiente', 'confirmada', 'activa'])->count();
@endphp

<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                    <span class="text-white font-semibold text-2xl">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </span>
                </div>
                
                <div>
                    <flux:heading size="xl">{{ $user->name }}</flux:heading>
                    <flux:subheading>
                        {{ $user->roles->isNotEmpty() ? ucfirst($user->roles->first()->name) : 'Sin rol' }}
                    </flux:subheading>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <flux:button :href="route('admin.users.edit', $user)" variant="primary" icon="pencil">
                    Editar
                </flux:button>
                
                <flux:button :href="route('admin.users.index')" variant="ghost" icon="arrow-left">
                    Volver
                </flux:button>
            </div>
        </div>

        {{-- Estadísticas --}}
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <x-stats.card title="Total Reservas" :value="$reservasCount" icon="clipboard-document-list" color="blue" />
            <x-stats.card title="Reservas Activas" :value="$reservasActivas" icon="clock" color="green" />
        </div>

        {{-- Información Personal --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <flux:heading size="lg" class="mb-4">Información Personal</flux:heading>
            
            <div class="space-y-4">
                <div class="flex justify-between py-3 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Nombre</span>
                    <span class="text-zinc-900 dark:text-zinc-100">{{ $user->name }}</span>
                </div>
                
                <div class="flex justify-between py-3 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Email</span>
                    <span class="text-zinc-900 dark:text-zinc-100">{{ $user->email }}</span>
                </div>
                
                @if($user->phone)
                <div class="flex justify-between py-3 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Teléfono</span>
                    <span class="text-zinc-900 dark:text-zinc-100">{{ $user->phone }}</span>
                </div>
                @endif
                
                <div class="flex justify-between py-3">
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Registrado</span>
                    <span class="text-zinc-900 dark:text-zinc-100">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
