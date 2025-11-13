<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['includeAppJs' => false])
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium group" wire:navigate>
                    <span class="flex h-12 w-12 mb-1 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 transition-all duration-300 group-hover:shadow-lg group-hover:shadow-blue-500/50">
                        <x-app-logo-icon class="size-7 fill-current text-white transition-transform duration-300 group-hover:scale-110" />
                    </span>
                    <span class="text-lg font-bold transition-colors duration-300 group-hover:text-blue-600 dark:group-hover:text-blue-400">BgaGO</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
