{{--
    Componente: Hero Section
    Propósito: Hero con diseño EXACTO del home actual
    Ubicación: resources/views/components/landing/hero.blade.php

    Props:
    - title: Título HTML
    - subtitle: Subtítulo
    - primaryCta: ['text' => '', 'route' => '']
    - secondaryCta: ['text' => '', 'route' => ''] (opcional)
    - stats: Array de estadísticas (opcional)
--}}

@props([
    'title',
    'subtitle',
    'primaryCta' => null,
    'secondaryCta' => null,
    'stats' => null
])

<section class="relative min-h-screen bg-gradient-to-br from-white via-blue-50 to-blue-200 overflow-hidden">
    <!-- Animación de fondo -->
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
        <div id="hero-animation" class="w-full h-full opacity-20"></div>
    </div>

    <!-- Contenedor principal -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

        <!-- Texto izquierda -->
        <div class="space-y-6">
            <h1 class="text-5xl md:text-7xl font-bold text-blue-500 leading-tight">
                {!! $title !!}
            </h1>

            <p class="text-xl md:text-2xl text-black/90 font-light">
                {{ $subtitle }}
            </p>

            <!-- CTAs -->
            @if($primaryCta || $secondaryCta)
                <div class="flex flex-col sm:flex-row gap-4 mt-8">
                    @if($primaryCta)
                        <a href="{{ route($primaryCta['route']) }}"
                           class="inline-flex items-center justify-center px-8 py-4 bg-blue-500 text-white rounded-xl font-semibold text-lg shadow-2xl hover:scale-105 hover:shadow-blue-500/20 transition-transform duration-300">
                            <span>{{ $primaryCta['text'] }}</span>
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform"
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
                    @endif

                    @if($secondaryCta)
                        <a href="{{ route($secondaryCta['route']) }}"
                           class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-sm text-blue-600 border-2 border-blue-500/30 rounded-xl font-semibold text-lg hover:bg-blue-500/20 transition-all duration-300">
                            {{ $secondaryCta['text'] }}
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <!-- Ilustración derecha -->
        <div class="relative">
            <x-hero-illustration />
        </div>
    </div>

    <!-- Estadísticas -->
    @if($stats)
        <div class="absolute bottom-0 left-0 w-full bg-white-500 py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-{{ count($stats) }} gap-8 text-center">
                @foreach($stats as $stat)
                    <div>
                        <div class="text-4xl md:text-5xl font-bold text-blue-500">
                            {{ $stat['value'] }}
                        </div>
                        <div class="text-blue-500/80 text-sm md:text-base">
                            {{ $stat['label'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-blue-500"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24"
             aria-hidden="true">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
    </div>
</section>
