{{--
    Componente: Catalog CTA (Ajustado)
    Propósito: Call-to-action del catálogo con diseño EXACTO
    Ubicación: resources/views/components/catalog/cta.blade.php

    Props:
    - title: Título del CTA
    - subtitle: Subtítulo
    - ctaText: Texto del botón
    - ctaRoute: Ruta del botón
--}}

@props([
    'title' => '¿Listo para reservar?',
    'subtitle' => 'Crea tu cuenta y comienza a disfrutar de nuestros vehículos ecológicos hoy mismo.',
    'ctaText' => 'Crear Cuenta Gratis',
    'ctaRoute' => 'register'
])

<div class="mt-16 text-center bg-gradient-to-r from-blue-50 to-blue-50 dark:from-blue-950/30 dark:to-blue-950/30 rounded-2xl p-12 border border-blue-200 dark:border-blue-800">
    <h3 class="text-3xl md:text-4xl font-bold text-blue-700 mb-4">
        {{ $title }}
    </h3>
    <p class="text-lg md:text-xl text-blue-600 mb-8 max-w-3xl mx-auto leading-relaxed">
        {{ $subtitle }}
    </p>
    <a href="{{ route($ctaRoute) }}"
       class="inline-flex items-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-lg transition-all hover:scale-105 shadow-2xl shadow-blue-600/30">
        {{ $ctaText }}
        <svg class="w-5 h-5 ml-2"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 7l5 5m0 0l-5 5m5-5H6" />
        </svg>
    </a>
</div>
