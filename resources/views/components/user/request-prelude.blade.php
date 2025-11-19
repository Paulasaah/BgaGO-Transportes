<section class="mt-8 grid gap-8 lg:grid-cols-2">
    <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-6" x-data="{ pickup: '', dropoff: '', routeTo: '{{ route('services.delivery') }}' }">
        <div class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5Z"/></svg>
            Bucaramanga, CO
        </div>
        <h3 class="text-2xl font-bold text-zinc-900 dark:text-white">Solicitar domicilio</h3>
        <div class="mt-5 space-y-4">
            <div class="relative">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 dark:text-zinc-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5Z"/></svg>
                </div>
                <input type="text" x-model="pickup" placeholder="Lugar de recogida" class="w-full h-11 rounded-lg ps-10 pe-3 bg-white dark:bg-white/10 text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 dark:placeholder-zinc-500 ring-1 ring-zinc-200 dark:ring-white/10 focus:ring-2 focus:ring-blue-600 transition" />
            </div>
            <div class="relative">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 dark:text-zinc-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M3 6h18v2H3V6Zm0 5h18v2H3v-2Zm0 5h18v2H3v-2Z"/></svg>
                </div>
                <input type="text" x-model="dropoff" placeholder="Lugar de entrega" class="w-full h-11 rounded-lg ps-10 pe-3 bg-white dark:bg-white/10 text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 dark:placeholder-zinc-500 ring-1 ring-zinc-200 dark:ring-white/10 focus:ring-2 focus:ring-blue-600 transition" />
            </div>
            <div class="mt-2">
                <a :href="routeTo + '?pickup=' + encodeURIComponent(pickup) + '&dropoff=' + encodeURIComponent(dropoff)" wire:navigate class="inline-flex items-center justify-center h-11 px-5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">Completar Detalles</a>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <a href="{{ route('catalog.index') }}" wire:navigate class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-5 flex items-center justify-between hover:shadow-sm transition">
            <div class="flex items-center gap-4">
                <div class="relative flex items-center justify-center size-12 rounded-xl ring-1 ring-blue-200/60 dark:ring-blue-400/20 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6 text-blue-700 dark:text-blue-300" fill="currentColor"><path d="M7 8h10v9a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V8Zm2-2a3 3 0 1 1 6 0v2H9V6Z"/></svg>
                </div>
                <div>
                    <div class="text-sm font-semibold text-zinc-900 dark:text-white">Ver catálogo</div>
                    <div class="text-xs text-zinc-600 dark:text-zinc-400">Explora vehículos y tarifas</div>
                </div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5 text-zinc-400 group-hover:text-zinc-500 dark:text-zinc-500 dark:group-hover:text-zinc-400" fill="currentColor"><path d="M10 5l7 7-7 7-1.4-1.4L14.2 12 8.6 6.4 10 5Z"/></svg>
        </a>
        <a href="{{ route('catalog.reservations') }}" wire:navigate class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-5 flex items-center justify-between hover:shadow-sm transition">
            <div class="flex items-center gap-4">
                <div class="relative flex items-center justify-center size-12 rounded-xl ring-1 ring-emerald-200/60 dark:ring-emerald-400/20 bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/30 dark:to-emerald-800/20">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6 text-emerald-700 dark:text-emerald-300" fill="currentColor"><path d="M4 5h16v4H4V5Zm0 6h16v8a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-8Zm5 5l2 2 4-4-1.4-1.4-2.6 2.6-0.6-0.6L9 14l0 2Z"/></svg>
                </div>
                <div>
                    <div class="text-sm font-semibold text-zinc-900 dark:text-white">Reservas</div>
                    <div class="text-xs text-zinc-600 dark:text-zinc-400">Próximas y recientes</div>
                </div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5 text-zinc-400 group-hover:text-zinc-500 dark:text-zinc-500 dark:group-hover:text-zinc-400" fill="currentColor"><path d="M10 5l7 7-7 7-1.4-1.4L14.2 12 8.6 6.4 10 5Z"/></svg>
        </a>
    </div>
    <x-catalog.featured-carousel />
</section>