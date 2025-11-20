{{-- resources/views/components/stats/card.blade.php --}}

@props(['title', 'value', 'icon', 'color' => 'blue'])

@php
    $colorClasses = [
        'blue' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
        'green' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
        'purple' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
        'red' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
    ];
@endphp

<div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">
                {{ $title }}
            </p>
            <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                {{ $value }}
            </p>
        </div>
        <div class="flex h-12 w-12 items-center justify-center rounded-lg {{ $colorClasses[$color] ?? $colorClasses['blue'] }}">
            @switch($icon)
                @case('clipboard-check')
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M16 4H9.5a2 2 0 00-1.8 1H8a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V6a2 2 0 00-2-2z"/></svg>
                @break
                @case('truck')
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7h11v8H2z"/><path d="M13 10h4l3 3v2h-7z"/><circle cx="5.5" cy="17.5" r="2"/><circle cx="16.5" cy="17.5" r="2"/></svg>
                @break
                @case('chart-column')
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2v18H3"/><path d="M8 9h2v12H8"/><path d="M13 5h2v16h-2"/><path d="M18 12h2v9h-2"/></svg>
                @break
                @case('wrench')
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 11-5.4 5.4L3 18.99 5.01 21l6.3-6.3a4 4 0 005.4-5.4l-2.01-2.01z"/></svg>
                @break
                @case('users')
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="8" r="3"/><circle cx="16" cy="8" r="3"/><path d="M2 20a6 6 0 0112 0"/><path d="M10 20a6 6 0 0112 0"/></svg>
                @break
                @case('user-group')
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="10" r="3"/><path d="M2 20a6 6 0 0112 0"/><path d="M10 20a6 6 0 0112 0"/></svg>
                @break
                @case('user-circle')
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="8" r="3"/><path d="M6 20a6 6 0 0112 0"/></svg>
                @break
                @case('shield-check')
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 4v6c0 5-4 8-8 8s-8-3-8-8V7l8-4z"/><path d="M9 12l2 2 4-4"/></svg>
                @break
                @case('x-circle')
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6"/><path d="M9 9l6 6"/></svg>
                @break
                @case('check-badge')
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                @break
                @case('clock')
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 7v6l4 2"/></svg>
                @break
                @case('clipboard-document-list')
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4H9.5a2 2 0 00-1.8 1H8a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V6a2 2 0 00-2-2z"/><path d="M9 8h6"/><path d="M9 12h6"/><path d="M9 16h4"/></svg>
                @break
                @default
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>
            @endswitch
        </div>
    </div>
</div>
