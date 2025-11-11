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
         class="py-24 bg-zinc-50 dark:bg-zinc-950"
         x-transition:enter="transition ease-out duration-700"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">

                <!-- Columna de texto -->
                <div x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 -translate-x-12"
                     x-transition:enter-end="opacity-100 translate-x-0">

                    <h2 class="text-4xl md:text-5xl font-bold text-zinc-900 dark:text-white mb-6">
                        {{ $title }}
                    </h2>

                    @foreach($description as $paragraph)
                        <p class="text-lg text-zinc-600 dark:text-zinc-400 mb-6">
                            {{ $paragraph }}
                        </p>
                    @endforeach

                    <!-- Grid de features pequeños (2x2) -->
                    @if(count($features) > 0)
                        <div class="grid grid-cols-2 gap-6">
                            @foreach($features as $feature)
                                <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 shadow-lg">
                                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                                        {{ $feature['value'] }}
                                    </div>
                                    <div class="text-zinc-600 dark:text-zinc-400">
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
                    <div class="aspect-square rounded-2xl bg-gradient-to-br from-blue-500 to-blue-500 p-1 shadow-2xl border-4 border-blue-500">
                        <!-- Logo con crecimiento solo al pasar el cursor -->
                        <div class="w-full h-full rounded-2xl bg-white dark:bg-zinc-900 flex items-center justify-center">
                            <x-app-logo-icon class="w-2/3 h-2/3 text-blue-600 dark:text-blue-400 transform transition-transform duration-300 hover:scale-110" />
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
