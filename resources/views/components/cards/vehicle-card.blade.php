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
    <div class="group rounded-2xl overflow-hidden ring-1 ring-zinc-200 dark:ring-white/10 bg-white/70 dark:bg-white/5 backdrop-blur hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-300 hover:-translate-y-2">

        <a href="{{ route('catalog.vehicle.detail', ['id' => $vehicleSlug]) }}" wire:navigate class="aspect-video bg-zinc-50/60 dark:bg-zinc-800/40 flex items-center justify-center p-6">
            @php
                $image = match($iconType) {
                    'electric' => asset('images/catalog/scooter_electrico.png'),
                    'manual' => asset('images/catalog/bicicleta_manual.png'),
                    'skate' => asset('images/catalog/patineta.png'),
                    default => asset('images/catalog/motocicleta.png')
                };
            @endphp
            <img src="{{ $image }}" alt="{{ $name }}" class="w-56 sm:w-64 object-contain" />
        </a>

        {{-- Contenido de la Card --}}
        <div class="p-6">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-3">
                <a href="{{ route('catalog.vehicle.detail', ['id' => $vehicleSlug]) }}" wire:navigate class="text-xl font-bold text-zinc-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                    {{ $name }}
                </a>
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

                <a href="{{ route('catalog.vehicle.detail', ['id' => $vehicleSlug]) }}" wire:navigate
                   class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-all inline-block text-center shadow-lg hover:shadow-xl hover:scale-105">
                    Ver detalles
                </a>
            </div>
        </div>
    </div>

@else
    {{-- Tarjeta Coming Soon --}}
    <div class="group rounded-2xl overflow-hidden ring-1 ring-dashed ring-zinc-300 dark:ring-zinc-700 bg-white/70 dark:bg-white/5 backdrop-blur">

        {{-- Ícono Coming Soon --}}
        <div class="aspect-video bg-zinc-50/60 dark:bg-zinc-900/40 flex items-center justify-center">
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
