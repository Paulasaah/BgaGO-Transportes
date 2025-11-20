<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <script>(function(){var t=localStorage.getItem('theme');if(!t){t='light';localStorage.setItem('theme',t);}document.documentElement.classList.toggle('dark',t==='dark');document.documentElement.setAttribute('data-theme',t);})();</script>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-zinc-900">
        <div class="relative grid h-dvh items-stretch lg:grid-cols-[3fr_2fr]">
            <div class="relative hidden lg:block">
                <img src="{{ asset('images/login/ciudad.jpg') }}" alt="Ciudad" class="absolute inset-0 w-full h-full object-cover" />
                <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-blue-600/50 via-blue-600/30 to-transparent"></div>
                <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-none shadow-xl ring-1 ring-zinc-200 p-6 md:p-10 w-[320px] md:w-[460px] lg:w-[520px] h-[272px] md:h-[384px] lg:h-[448px] flex items-center justify-center">
                    <div class="space-y-3 text-center">
                        <div class="text-2xl md:text-3xl font-bold text-zinc-900">Transporte a tu alcance:</div>
                        <div class="text-2xl md:text-3xl font-bold text-zinc-900">motos, patinetas, bicicletas</div>
                        <div class="text-2xl md:text-3xl font-bold text-zinc-900">y patines.</div>
                        <div class="text-2xl md:text-3xl font-medium text-blue-600">¡Reserva fácil, muévete rápido!</div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-center justify-center p-6 md:p-10 bg-white dark:bg-zinc-900 relative">
                <div class="absolute top-4 right-4">
                    <div x-data="{ theme: localStorage.getItem('theme') || 'light', init(){ document.documentElement.classList.toggle('dark', this.theme==='dark'); document.documentElement.setAttribute('data-theme', this.theme) }, toggle(){ this.theme = this.theme==='dark' ? 'light' : 'dark'; localStorage.setItem('theme', this.theme); document.documentElement.classList.toggle('dark', this.theme==='dark'); document.documentElement.setAttribute('data-theme', this.theme) } }">
                        <button @click="toggle" :aria-label="theme==='dark' ? 'Cambiar a claro' : 'Cambiar a oscuro'" class="relative h-9 w-16 rounded-full transition-colors" :class="theme==='dark' ? 'bg-[#0b2a3a]' : 'bg-amber-100'">
                            <span class="absolute top-1 left-1 h-7 w-7 rounded-full flex items-center justify-center text-white transition-all" :class="theme==='dark' ? 'translate-x-7 bg-sky-500' : 'translate-x-0 bg-amber-400 text-black'">
                                <template x-if="theme==='dark'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"></path></svg>
                                </template>
                                <template x-if="theme==='light'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path></svg>
                                </template>
                            </span>
                        </button>
                    </div>
                </div>
                <a href="{{ route('home') }}" class="flex items-center gap-2 font-medium mb-8" wire:navigate>
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700">
                        <x-app-logo-icon class="size-7 fill-current text-white" />
                    </span>
                    <span class="text-lg font-bold text-zinc-900 dark:text-white">BgaGO</span>
                </a>
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
