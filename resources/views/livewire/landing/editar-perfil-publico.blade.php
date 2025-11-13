<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
        $this->redirectRoute('perfil-usuario', navigate: false);
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
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
                    <h2 class="text-lg font-semibold text-blue-600 dark:text-blue">Profile</h2>
                    <p class="text-sm text-blue-600 dark:text-blue-400">Actualiza tu nombre y correo electrónico</p>
                </div>

                <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6 bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nombre</label>
                        <input id="name" wire:model="name" type="text" name="name" required autofocus autocomplete="name" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

            <div>
                <label for="email" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Correo electrónico</label>
                <input id="email" wire:model="email" type="email" name="email" required autocomplete="email" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                    <div class="mt-2 text-sm text-zinc-800 dark:text-zinc-200">
                        <span>Tu correo electrónico no está verificado.</span>
                        <button wire:click.prevent="resendVerificationNotification" class="ml-1 rounded-md text-sm text-blue-600 underline hover:text-blue-700 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Haz clic aquí para reenviar el correo de verificación.
                        </button>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                                Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
                            </p>
                        @endif
                    </div>
                @endif
            </div>

                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-end w-full">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">Guardar</button>
                        </div>

                        <x-action-message class="me-3" on="profile-updated">
                            Guardado.
                        </x-action-message>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
