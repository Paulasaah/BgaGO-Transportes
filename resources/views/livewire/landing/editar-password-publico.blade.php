<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->dispatch('password-updated');
        $this->redirectRoute('perfil-usuario', navigate: false);
    }
}; ?>

<section class="py-24 min-h-screen bg-gradient-to-br from-white via-blue-50 to-blue-200 overflow-hidden">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-blue-600 dark:text-blue">Settings</h1>
            <p class="text-sm text-blue-600 dark:text-blue-400">Administra tu perfil y configuración</p>
        </div>

        <div class="flex items-start max-md:flex-col">
            <div class="mr-10 w-full pb-4 md:w-[220px]">
                <nav class="space-y-1">
                    <a href="{{ route('editar-perfil-publico') }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('editar-perfil-publico') ? 'bg-blue-500 text-blue-700 dark:bg-blue-950/40 dark:text-blue-500' : 'text-blue-500 hover:bg-blue-500 dark:text-blue-300 dark:hover:bg-blue-950/30' }}">Profile</a>
                    <a href="{{ route('editar-password-publico') }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('editar-password-publico') ? 'bg-blue-500 text-blue-700 dark:bg-blue-950/40 dark:text-blue-500' : 'text-blue-500 hover:bg-blue-500 dark:text-blue-300 dark:hover:bg-blue-950/30' }}">Password</a>
                </nav>
            </div>

            <div class="flex-1 self-stretch max-md:pt-6">
                <div>
                    <h2 class="text-lg font-semibold text-blue-600 dark:text-blue">Password</h2>
                    <p class="text-sm text-blue-600 dark:text-blue-400">Actualiza tu contraseña</p>
                </div>

                <form wire:submit="updatePassword" class="my-6 w-full space-y-6 bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Contraseña actual</label>
                        <input id="current_password" wire:model="current_password" type="password" name="current_password" autocomplete="current-password" required class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nueva contraseña</label>
                        <input id="password" wire:model="password" type="password" name="password" autocomplete="new-password" required class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Confirmar contraseña</label>
                        <input id="password_confirmation" wire:model="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-end w-full">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">Guardar</button>
                        </div>

                        <x-action-message class="me-3" on="password-updated">
                            Guardado.
                        </x-action-message>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
