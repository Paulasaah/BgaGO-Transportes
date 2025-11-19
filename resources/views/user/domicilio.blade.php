<x-layouts.public>
    <div class="min-h-screen bg-gradient-to-br from-white via-blue-50 to-blue-100 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-800 py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <a href="{{ route('catalog.index') }}" wire:navigate class="inline-flex items-center justify-center h-10 px-4 rounded-lg ring-1 ring-zinc-300 dark:ring-white/10 text-blue-600 dark:text-blue-400 hover:bg-zinc-50 dark:hover:bg-white/10 mb-4 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Volver al Catálogo
                </a>
                <h1 class="text-4xl md:text-5xl font-bold text-zinc-900 dark:text-white mb-4">Solicitar Domicilio</h1>
                <p class="text-lg text-zinc-600 dark:text-zinc-400">Ingresa los datos para programar tu domicilio</p>
            </div>

            <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white/70 dark:bg-white/5 backdrop-blur overflow-hidden">
                @livewire('formulario-domicilio')
            </div>
        </div>
    </div>
</x-layouts.public>