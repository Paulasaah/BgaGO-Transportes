{{--
    Componente: Services Grid (CORREGIDO)
    Propósito: Grid de servicios con diseño EXACTO del home
    Ubicación: resources/views/components/landing/services-grid.blade.php

    Props:
    - title: Título de la sección
    - subtitle: Subtítulo
    - services: Array de servicios completos
      Estructura esperada:
      [
        [
          'iconType' => 'prestamo',
          'title' => 'Título',
          'description' => 'Descripción',
          'features' => ['feat1', 'feat2'],
          'ctaText' => 'Texto',
          'ctaRoute' => 'ruta'
        ],
        ...
      ]
--}}

@props([
    'title',
    'subtitle',
    'services' => []
])

<section id="servicios" class="overflow-visible" x-data="{ inView: false }">
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
                <h2 class="text-4xl md:text-5xl font-bold text-zinc-900 dark:text-white mb-4">
                    {{ $title }}
                </h2>
                <p class="text-xl text-zinc-600 dark:text-zinc-400 max-w-2xl mx-auto">
                    {{ $subtitle }}
                </p>
            </div>

            <!-- Grid de servicios (3 columnas) -->
            @if(count($services) > 0)
                <div class="grid md:grid-cols-3 gap-8">
                    @foreach($services as $index => $service)
                        <div x-show="inView"
                             x-transition:enter="transition ease-out duration-700"
                             x-transition:enter-start="opacity-0 translate-y-8"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             style="transition-delay: {{ ($index + 1) * 100 }}ms;">
                            <x-cards.service-card
                                :iconType="$service['iconType'] ?? 'prestamo'"
                                :title="$service['title']"
                                :description="$service['description']"
                                :features="$service['features'] ?? []"
                                :ctaText="$service['ctaText'] ?? 'Ver más'"
                                :ctaRoute="$service['ctaRoute'] ?? 'home'"
                            />
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Estado vacío --}}
                <div class="text-center py-12">
                    <p class="text-zinc-500 dark:text-zinc-400">No hay servicios disponibles</p>
                </div>
            @endif

        </div>
    </div>
</section>
