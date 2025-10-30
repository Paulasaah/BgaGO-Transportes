{{-- resources/views/components/table/pagination.blade.php --}}
@props([
    'links' => null, // Para paginación de Laravel: $items->links()
])

<div {{ $attributes->merge(['class' => 'flex items-center justify-between border-t border-zinc-200 dark:border-zinc-800 px-4 py-3 bg-white dark:bg-zinc-900 sm:px-6']) }}>
    <div class="flex-1 flex justify-between sm:hidden">
        {{-- Versión móvil --}}
        @if ($links)
            {{ $links->onEachSide(0)->links('pagination::simple-tailwind') }}
        @endif
    </div>

    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        @if ($links)
            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                Mostrando <span class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $links->firstItem() }}</span>
                a <span class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $links->lastItem() }}</span>
                de <span class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $links->total() }}</span> registros
            </div>
            <div>
                {{ $links->onEachSide(1)->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
