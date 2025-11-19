{{--
    Componente: Locations Section
    Propósito: Sección de ubicaciones con mapa y lista, diseño EXACTO del home
    Ubicación: resources/views/components/landing/locations-section.blade.php

    Props:
    - title: Título de la sección
    - subtitle: Subtítulo
    - locations: Array de ubicaciones
    - mapRoute: Ruta al mapa completo
--}}

@props([
    'title',
    'subtitle',
    'locations' => [],
    'mapRoute' => 'mapa',
    'mapUrl' => '/mapa'
])

<section id="sedes" class="overflow-visible" x-data="{ inView: false }">
    <!-- Sentinel invisible -->
    <div x-intersect.once="inView = true" class="h-px w-px"></div>

    <div x-show="inView"
         x-cloak
         class="py-24 bg-white dark:bg-zinc-900"
         x-transition:enter="transition ease-out duration-700"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Encabezado -->
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold text-blue-600 dark:text-blue-400 mb-4">
                    {{ $title }}
                </h2>
                <p class="text-base text-zinc-600 dark:text-zinc-400 max-w-2xl mx-auto">
                    {{ $subtitle }}
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-12 mb-12">

                <!-- Map Placeholder -->
                <div class="order-2 md:order-1"
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 -translate-x-12"
                     x-transition:enter-end="opacity-100 translate-x-0">

                    <div class="rounded-2xl p-8 h-full flex items-center justify-center border border-zinc-200 dark:border-white/10 bg-zinc-100 dark:bg-zinc-800/60 text-zinc-800 dark:text-white">
                        <div class="text-center">
                            <svg class="w-24 h-24 mx-auto text-zinc-500 dark:text-zinc-400 mb-4"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24"
                                 aria-hidden="true">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            <p class="text-lg text-zinc-700 dark:text-zinc-300">
                                Mapa Interactivo
                            </p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-2">
                                Visualiza nuestras sedes en tiempo real
                            </p>
                            <a href="{{ (isset($mapUrl) && $mapUrl) ? $mapUrl : (\Illuminate\Support\Facades\Route::has($mapRoute) ? route($mapRoute) : '/mapa') }}"
                               class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors">
                                Ver Mapa Completo
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Locations List -->
                <div class="order-1 md:order-2 space-y-6"
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 translate-x-12"
                     x-transition:enter-end="opacity-100 translate-x-0">

                    @foreach($locations as $location)
                        <x-cards.location-card
                            :name="$location['name']"
                            :zone="$location['zone']"
                            :address="$location['address']"
                            :phone="$location['phone']"
                            :schedule="$location['schedule']"
                            :status="$location['status'] ?? 'open'"
                            :variant="$location['variant'] ?? 'default'"
                        />
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</section>
