{{-- resources/views/components/badges/status.blade.php --}}
@props([
    'status' => 'inactive',
    'label' => null,
])

@php
    $colors = [
        'active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        'inactive' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        'default' => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300',
    ];

    $color = $colors[$status] ?? $colors['default'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-md $color"]) }}>
    @if($status === 'active')
        {{-- ✅ Check Circle --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4m5 2a9 9 0 11-18 0a9 9 0 0118 0z" />
        </svg>
    @elseif($status === 'inactive')
        {{-- ❌ X Circle --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2l2 2m0-4l-2 2l-2-2m5 2a9 9 0 11-18 0a9 9 0 0118 0z" />
        </svg>
    @elseif($status === 'pending')
        {{-- 🕓 Clock --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0a9 9 0 0118 0z" />
        </svg>
    @endif

    {{ $label ?? ucfirst($status) }}
</span>
