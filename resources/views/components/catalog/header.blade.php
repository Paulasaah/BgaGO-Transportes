{{--
    Componente: Catalog Header (Ajustado)
    Propósito: Encabezado del catálogo con diseño EXACTO
    Ubicación: resources/views/components/catalog/header.blade.php

    Props:
    - title: Título principal
    - subtitle: Subtítulo descriptivo
--}}

@props([
    'title' => 'Catálogo de Transporte Ecológico',
    'subtitle' => 'Explora nuestra flota de bicicletas, motos y patinetas eléctricas disponibles para préstamo.'
])

<div class="text-center mb-12">
    <h1 class="text-4xl md:text-5xl font-bold text-blue-700 dark:text-blue-700 mb-4">
        {{ $title }}
    </h1>
    <p class="text-xl text-blue-500 dark:text-blue-500 max-w-2xl mx-auto">
        {{ $subtitle }}
    </p>
</div>
