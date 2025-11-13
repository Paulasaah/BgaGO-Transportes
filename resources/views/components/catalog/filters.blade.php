{{--
    Componente: Catalog Filters (Ajustado)
    Propósito: Botones de filtro del catálogo con diseño EXACTO
    Ubicación: resources/views/components/catalog/filters.blade.php

    Props:
    - filters: Array de filtros ['Todos', 'Bicicletas', 'Motos', ...]
    - activeFilter: Filtro activo actualmente (default: 'Todos')
--}}

@props([
    'filters' => ['Todos', 'Bicicletas', 'Motos', 'Patinetas'],
    'activeFilter' => 'Todos'
])

@php
    $map = [
        'Todos' => null,
        'Bicicletas' => 'bicicleta',
        'Motos' => 'moto',
        'Patinetas' => 'patineta',
    ];
    $activeTipo = request('tipo');
@endphp

<div class="flex flex-wrap gap-3 mb-12 justify-center">
    @foreach($filters as $label)
        @php
            $value = $map[$label] ?? null;
            $isActive = ($value === null && !$activeTipo) || ($value === $activeTipo);
            $href = $value ? route('catalogo', ['tipo' => $value]) : route('catalogo');
        @endphp
        <a href="{{ $href }}"
           class="px-6 py-2.5 rounded-full font-medium transition-all duration-200 shadow-md {{ $isActive ? 'bg-blue-600 text-white' : 'bg-zinc-800 text-zinc-300 hover:bg-zinc-700' }}">
            {{ $label }}
        </a>
    @endforeach
</div>
