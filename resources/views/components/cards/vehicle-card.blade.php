{{--
    Componente: Vehicle Card (FUNCIONAL)
    Ubicación: resources/views/components/cards/vehicle-card.blade.php

    Uso en catálogo:
    <x-cards.vehicle-card
        name="Bicicleta Eléctrica"
        description="Perfecta para trayectos urbanos."
        iconType="electric"
        status="available"
        :price="5000"
        :specs="['Eléctrica', '50km', '18kg']"
        vehicleSlug="bicicleta-electrica"
    />
--}}

@props([
    'name',
    'description',
    'iconType' => 'electric',
    'status' => 'available',
    'price' => null,
    'specs' => [],
    'vehicleSlug' => ''
])

@if($status === 'available')
    {{-- Tarjeta de Vehículo Disponible --}}
    <div class="group bg-white dark:bg-zinc-800 rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-700 hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-300 hover:-translate-y-2">

        {{-- Ícono del Vehículo --}}
        <div class="aspect-video bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-700 dark:to-zinc-800 flex items-center justify-center p-12">
            @if($iconType === 'electric')
                <svg class="w-20 h-20 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            @elseif($iconType === 'manual')
                <svg class="w-20 h-20 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                </svg>
            @elseif($iconType === 'skate')
                <svg class="w-20 h-20 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
                </svg>
            @else
                <svg class="w-20 h-20 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            @endif
        </div>

        {{-- Contenido de la Card --}}
        <div class="p-6">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xl font-bold text-zinc-900 dark:text-white">
                    {{ $name }}
                </h3>
                <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-semibold rounded-md">
                    Disponible
                </span>
            </div>

            {{-- Descripción --}}
            <p class="text-zinc-600 dark:text-zinc-400 text-sm mb-4 leading-relaxed">
                {{ $description }}
            </p>

            {{-- Especificaciones --}}
            @if(count($specs) > 0)
                <div class="flex items-center justify-between text-sm text-zinc-500 dark:text-zinc-400 mb-6 gap-2">
                    @foreach($specs as $spec)
                        <span class="whitespace-nowrap">{{ $spec }}</span>
                    @endforeach
                </div>
            @endif

            {{-- Precio y Botón Reservar --}}
            <div class="flex items-center justify-between pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <div>
                    <div class="text-3xl font-bold text-zinc-900 dark:text-white">
                        ${{ number_format($price, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">por hora</div>
                </div>

                {{-- ⭐ BOTÓN RESERVAR - LINK FUNCIONAL ⭐ --}}
                <a href="{{ route('reservar') }}?vehiculo={{ $vehicleSlug }}&precio={{ $price }}"
                   class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-all inline-block text-center shadow-lg hover:shadow-xl hover:scale-105">
                    Reservar
                </a>
            </div>
        </div>
    </div>

@else
    {{-- Tarjeta Coming Soon --}}
    <div class="group bg-white dark:bg-zinc-800 rounded-2xl overflow-hidden border-2 border-dashed border-zinc-300 dark:border-zinc-700">

        {{-- Ícono Coming Soon --}}
        <div class="aspect-video bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center">
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto text-zinc-400 dark:text-zinc-600 mb-3"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <p class="text-zinc-500 dark:text-zinc-400 font-medium text-sm">
                    Próximamente
                </p>
            </div>
        </div>

        {{-- Contenido Coming Soon --}}
        <div class="p-6">
            <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-2">
                Próximamente
            </h3>
            <p class="text-zinc-600 dark:text-zinc-400 text-sm">
                Estamos trabajando en agregar más opciones a nuestro catálogo.
            </p>
        </div>
    </div>
@endif
