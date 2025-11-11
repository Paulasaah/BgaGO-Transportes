{{--
    Componente: Service Card (CORREGIDO)
    Propósito: Tarjeta de servicio con íconos predefinidos
    Ubicación: resources/views/components/cards/service-card.blade.php

    Props:
    - iconType: Tipo de ícono ('prestamo', 'domicilio', 'gps', 'custom')
    - title: Título del servicio
    - description: Descripción del servicio
    - features: Array de características ['feature1', 'feature2', ...]
    - ctaText: Texto del botón
    - ctaRoute: Ruta del botón
--}}

@props([
    'iconType' => 'prestamo',
    'title',
    'description',
    'features' => [],
    'ctaText' => 'Explorar',
    'ctaRoute' => '#'
])

<div class="group bg-white dark:bg-zinc-800 rounded-2xl p-8 border border-zinc-200 dark:border-zinc-700 hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-300 hover:-translate-y-2">

    {{-- Ícono --}}
    <div class="mb-6 w-16 h-16 bg-blue-100 dark:bg-blue-950/50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
        @if($iconType === 'prestamo')
            {{-- Ícono de Bicicleta/Vehículo --}}
            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
        @elseif($iconType === 'domicilio')
            {{-- Ícono de Entrega/Paquete --}}
            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
        @elseif($iconType === 'gps')
            {{-- Ícono de GPS/Ubicación --}}
            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        @elseif($iconType === 'electric')
            {{-- Ícono de Eléctrico/Rayo --}}
            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
        @elseif($iconType === 'clock')
            {{-- Ícono de Reloj/24-7 --}}
            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        @elseif($iconType === 'shield')
            {{-- Ícono de Seguridad/Shield --}}
            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        @else
            {{-- Ícono por defecto --}}
            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
        @endif
    </div>

    {{-- Título --}}
    <h3 class="text-2xl font-bold text-zinc-900 dark:text-white mb-3">
        {{ $title }}
    </h3>

    {{-- Descripción --}}
    <p class="text-zinc-600 dark:text-zinc-400 mb-6 leading-relaxed">
        {{ $description }}
    </p>

    {{-- Lista de características --}}
    @if(count($features) > 0)
        <ul class="space-y-2 mb-6">
            @foreach($features as $feature)
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-zinc-600 dark:text-zinc-400 text-sm">{{ $feature }}</span>
                </li>
            @endforeach
        </ul>
    @endif

    {{-- CTA Button --}}
    <a href="{{ route($ctaRoute) }}"
       class="inline-flex items-center text-blue-600 dark:text-blue-400 font-semibold hover:text-blue-700 dark:hover:text-blue-300 transition-colors group/link">
        {{ $ctaText }}
        <svg class="w-5 h-5 ml-2 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
        </svg>
    </a>
</div>
