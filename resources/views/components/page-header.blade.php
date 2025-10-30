{{-- resources/views/components/page-header.blade.php --}}
@props([
    'title',
    'subtitle' => null,
    'buttonText' => null,
    'buttonIcon' => 'plus',
    'buttonAction' => null,
])

<header {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6']) }}>
    <div>
        <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $subtitle }}</p>
        @endif
    </div>

    @if($buttonText && $buttonAction)
        <flux:button 
            wire:click="{{ $buttonAction }}"
            icon="{{ $buttonIcon }}"
            class="self-start sm:self-auto"
        >
            {{ $buttonText }}
        </flux:button>
    @endif
</header>
