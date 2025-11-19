<section class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-6 transition hover:shadow-lg">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Reservar vehículo</h3>
        <p class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">Elige motos, patinetas, bicicletas o patines.</p>
        <div class="mt-4">
            <a href="{{ route('catalog.index') }}" wire:navigate class="inline-flex items-center justify-center px-4 h-10 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">Reservar ahora</a>
        </div>
    </div>
    <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-6 transition hover:shadow-lg">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Ver catálogo</h3>
        <p class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">Explora disponibilidad y tarifas actualizadas.</p>
        <div class="mt-4">
            <a href="{{ route('catalog.index') }}" wire:navigate class="inline-flex items-center justify-center px-4 h-10 rounded-lg ring-1 ring-zinc-300 dark:ring-white/10 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-white/10 transition-colors">Ver catálogo</a>
        </div>
    </div>
    <div class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-white/10 bg-white dark:bg-zinc-900 p-6 transition hover:shadow-lg">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Mis reservas</h3>
        <p class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">Consulta próximas y últimas reservas.</p>
        <div class="mt-4">
            <a href="{{ route('catalog.reservations') }}" wire:navigate class="inline-flex items-center justify-center px-4 h-10 rounded-lg ring-1 ring-zinc-300 dark:ring-white/10 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-white/10 transition-colors">Ver reservas</a>
        </div>
    </div>
</section>