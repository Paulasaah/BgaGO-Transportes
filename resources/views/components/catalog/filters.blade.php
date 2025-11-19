{{--
    Componente: Catalog Filters (Ajustado)
    Propósito: Botones de filtro del catálogo con diseño EXACTO
    Ubicación: resources/views/components/catalog/filters.blade.php

    Props:
    - filters: Array de filtros ['Todos', 'Bicicletas', 'Motos', ...]
    - activeFilter: Filtro activo actualmente (default: 'Todos')
--}}

@props([
    'filters' => ['Todos', 'Bicicletas', 'Motos', 'Patinetas', 'Patines'],
    'activeFilter' => 'Todos'
])

<div class="flex flex-wrap gap-3 mb-12 justify-center" x-data="{ active: '{{ $activeFilter }}' }">
    @foreach($filters as $filter)
        <button
            @click="active = '{{ $filter }}'"
            :class="active === '{{ $filter }}'
                ? 'bg-blue-600 text-white'
                : 'bg-zinc-800 text-zinc-300 hover:bg-zinc-700'"
            class="px-6 py-2.5 rounded-full font-medium transition-all duration-200 shadow-md">
            {{ $filter }}
        </button>
    @endforeach
</div>
