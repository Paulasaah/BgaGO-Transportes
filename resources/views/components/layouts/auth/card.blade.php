<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
<body class="min-h-screen bg-white antialiased dark:bg-black">
        <div class="min-h-svh grid grid-cols-1 md:grid-cols-5">
            <div class="relative md:col-span-3">
                <img src="{{ asset('images/about/city-night.jpg') }}" alt="Ciudad" class="absolute inset-0 w-full h-full object-cover" />
                <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-blue-600/50 via-blue-600/30 to-transparent"></div>
                <div class="absolute left-6 md:left-12 top-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-xl ring-1 ring-zinc-200 p-6 md:p-10 max-w-xl">
                    <div class="space-y-3">
                        <div class="text-2xl md:text-3xl font-bold text-zinc-900">Transporte a tu alcance:</div>
                        <div class="text-2xl md:text-3xl font-bold text-zinc-900">motos, patinetas, bicicletas</div>
                        <div class="text-2xl md:text-3xl font-bold text-zinc-900">y patines.</div>
                        <div class="text-2xl md:text-3xl font-semibold text-blue-600">¡Reserva fácil, muévete rápido!</div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2 flex flex-col items-center justify-center p-6 md:p-10">
                <a href="{{ route('home') }}" class="flex items-center gap-2 font-medium mb-8" wire:navigate>
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700">
                        <x-app-logo-icon class="size-7 fill-current text-white" />
                    </span>
                    <span class="text-lg font-bold text-zinc-900 dark:text-white">BgaGO</span>
                </a>

                <div class="w-full max-w-md">
                    <div class="rounded-xl border bg-white dark:bg-stone-950 dark:border-stone-800 text-stone-800 shadow-xs">
                        <div class="px-6 md:px-10 py-8">{{ $slot }}</div>
                    </div>
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
