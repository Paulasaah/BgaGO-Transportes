{{--
    Componente: Location Card
    Propósito: Tarjeta de ubicación con diseño EXACTO del home
    Ubicación: resources/views/components/cards/location-card.blade.php

    Props:
    - name: Nombre de la sede
    - zone: Descripción de la zona
    - address: Dirección
    - phone: Teléfono
    - schedule: Horario
    - status: 'open' o 'closed'
    - variant: 'primary', 'default', 'secondary', 'tertiary'
--}}

@props([
    'name',
    'zone',
    'address',
    'phone',
    'schedule',
    'status' => 'open',
    'variant' => 'default'
])

@php
    $variants = [
        'primary' => 'bg-gradient-to-br from-blue-500 to-blue-50 dark:from-blue-950/50 dark:to-blue-950/50',
        'default' => 'bg-gradient-to-br from-blue-50 to-blue-50 dark:from-blue-950/50 dark:to-blue-950/50',
        'secondary' => 'bg-gradient-to-br from-blue-500 to-blue-50 dark:from-blue-950/50 dark:to-blue-950/50',
        'tertiary' => 'bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/50 dark:to-indigo-950/50'
    ];

    $variantClass = $variants[$variant] ?? $variants['default'];

    $statusBadge = $status === 'open'
        ? 'bg-blue-500 text-white'
        : 'bg-red-500 text-white';

    $statusText = $status === 'open' ? 'Abierto' : 'Cerrado';
@endphp

<div class="{{ $variantClass }} rounded-xl p-6 border border-blue-200 dark:border-blue-800 hover:shadow-lg transition-shadow">

    <!-- Encabezado -->
    <div class="flex items-start justify-between mb-3">
        <div>
            <h3 class="text-xl font-bold text-zinc-900 dark:text-white">
                {{ $name }}
            </h3>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                {{ $zone }}
            </p>
        </div>
        <div class="{{ $statusBadge }} text-xs font-semibold px-3 py-1 rounded-full">
            {{ $statusText }}
        </div>
    </div>

    <!-- Información de contacto -->
    <div class="space-y-2 text-sm text-zinc-600 dark:text-zinc-400">

        <!-- Dirección -->
        <p class="flex items-center">
            <svg class="w-4 h-4 mr-2 text-blue-600 dark:text-blue-400 flex-shrink-0"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24"
                 aria-hidden="true">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            </svg>
            {{ $address }}
        </p>

        <!-- Teléfono -->
        <p class="flex items-center">
            <svg class="w-4 h-4 mr-2 text-blue-600 dark:text-blue-400 flex-shrink-0"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24"
                 aria-hidden="true">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            {{ $phone }}
        </p>

        <!-- Horario -->
        <p class="flex items-center">
            <svg class="w-4 h-4 mr-2 text-blue-600 dark:text-blue-400 flex-shrink-0"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24"
                 aria-hidden="true">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ $schedule }}
        </p>
    </div>
</div>
