{{--
    Componente: Service Card
    Propósito: Tarjeta de servicio con diseño EXACTO del home
    Ubicación: resources/views/components/cards/service-card.blade.php

    Props:
    - iconType: 'calendar', 'package', 'map' (identificador del ícono)
    - title: Título del servicio
    - description: Descripción
    - features: Array de características ['text']
    - ctaText: Texto del botón CTA
    - ctaRoute: Ruta del CTA
--}}

@props([
    'iconType',
    'title',
    'description',
    'features' => [],
    'ctaText',
    'ctaRoute'
])

@php
    $icons = [
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
        'package' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />',
        'map' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />'
    ];

    $iconPath = $icons[$iconType] ?? $icons['calendar'];
@endphp

<div class="group relative bg-gradient-to-br from-zinc-50 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900 rounded-2xl p-8 hover:shadow-2xl transition-all duration-300 border border-zinc-200 dark:border-zinc-700 hover:-translate-y-2">

    <!-- Ícono -->
    <div class="w-16 h-16 bg-blue-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            {!! $iconPath !!}
        </svg>
    </div>

    <!-- Título -->
    <h3 class="text-2xl font-bold text-zinc-900 dark:text-white mb-3">
        {{ $title }}
    </h3>

    <!-- Descripción -->
    <p class="text-zinc-600 dark:text-zinc-400 mb-6">
        {{ $description }}
    </p>

    <!-- Lista de características -->
    @if(count($features) > 0)
        <ul class="space-y-2 mb-6">
            @foreach($features as $feature)
                <li class="flex items-center text-sm text-zinc-600 dark:text-zinc-400">
                    <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24"
                         aria-hidden="true">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M5 13l4 4L19 7" />
                    </svg>
                    {{ is_array($feature) ? ($feature['text'] ?? '') : $feature }}
                </li>
            @endforeach
        </ul>
    @endif

    <!-- CTA -->
    <a href="{{ route($ctaRoute) }}"
       class="inline-flex items-center text-blue-600 dark:text-blue-400 font-semibold group-hover:translate-x-2 transition-transform">
        {{ $ctaText }}
        <svg class="w-5 h-5 ml-2"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24"
             aria-hidden="true">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 7l5 5m0 0l-5 5m5-5H6" />
        </svg>
    </a>
</div>
