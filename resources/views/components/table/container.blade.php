{{-- resources/views/components/table/container.blade.php --}}
@props([
    'showFilters' => true,
])

<div {{ $attributes->merge([
    'class' => 'bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden'
]) }}>
    @if($showFilters && isset($filters))
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-800">
            {{ $filters }}
        </div>
    @endif

    <div class="overflow-x-auto">
        {{ $slot }}
    </div>
</div>
