{{--
    Componente: About Section
    Propósito: Sección About con diseño EXACTO del home actual
    Ubicación: resources/views/components/landing/about-section.blade.php

    Props:
    - title: Título de la sección
    - description: Array de párrafos
    - features: Array pequeño (máx 4) con ['value', 'label']
--}}

@props([
    'title',
    'description' => [],
    'features' => []
])

<section class="overflow-visible" x-data="{ inView: false }">
    <!-- Trigger de intersección -->
    <div x-intersect.once="inView = true" class="h-px w-px"></div>

    <div x-show="inView"
         x-cloak
         class="py-24 bg-white dark:bg-black"
         x-transition:enter="transition ease-out duration-700"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">

                <!-- Columna de texto -->
                <div x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 -translate-x-12"
                     x-transition:enter-end="opacity-100 translate-x-0">

                    <h2 class="text-4xl md:text-5xl font-bold text-blue-600 dark:text-blue-400 mb-6">
                        {{ $title }}
                    </h2>

                    @foreach($description as $paragraph)
                        <p class="text-lg text-zinc-700 dark:text-zinc-300 mb-6">
                            {{ $paragraph }}
                        </p>
                    @endforeach

                    <!-- Grid de features pequeños (2x2) -->
                    @if(count($features) > 0)
                        <div class="grid grid-cols-2 gap-6">
                            @foreach($features as $feature)
                                <div class="rounded-xl p-6 bg-white dark:bg-blue-600 ring-1 ring-zinc-200 dark:ring-white/10">
                                    <div class="text-3xl font-bold text-blue-600 dark:text-white mb-2">
                                        {{ $feature['value'] }}
                                    </div>
                                    <div class="text-blue-600 dark:text-white">
                                        {{ $feature['label'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Columna visual: Cuadrado con logo (diseño EXACTO) -->
                <div class="relative"
                     x-transition:enter="transition ease-out duration-700 delay-200"
                     x-transition:enter-start="opacity-0 translate-x-12"
                     x-transition:enter-end="opacity-100 translate-x-0">

                    <!-- Cuadrado con borde azul eléctrico, sin borde circular -->
                    <div class="aspect-square rounded-2xl p-1 shadow-2xl ring-1 ring-zinc-200 dark:ring-black bg-zinc-900/60">
                        <img src="{{ asset('images/about/city-night.jpg') }}" alt="Vista nocturna urbana con scooter" class="w-full h-full object-cover rounded-2xl" />
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
