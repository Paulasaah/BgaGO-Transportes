{{-- resources/views/components/table/header.blade.php --}}
@props([
    'columns' => [],
])

<thead {{ $attributes->merge(['class' => 'bg-zinc-50 dark:bg-zinc-800 text-left text-sm text-zinc-600 dark:text-zinc-300 uppercase']) }}>
    <tr>
        @foreach($columns as $col)
            <th scope="col" class="px-6 py-3 font-medium {{ $col['class'] ?? '' }}">
                {{ $col['label'] ?? '' }}
            </th>
        @endforeach
    </tr>
</thead>
