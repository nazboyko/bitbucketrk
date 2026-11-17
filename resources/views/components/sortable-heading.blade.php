@use('App\Enums\SortDirection')

@props(['option', 'current', 'direction'])

<th
    wire:click="sortBy('{{ $option->value }}')"
    {{ $attributes->class('cursor-pointer select-none px-4 py-3 font-medium') }}
>
    <span class="inline-flex items-center gap-1">
        {{ $slot }}

        <svg
            viewBox="0 0 12 12"
            fill="currentColor"
            @class([
                'size-3',
                'invisible' => $option !== $current,
                'rotate-180' => $direction === SortDirection::Descending,
            ])
        >
            <path d="M6 3l4 6H2z" />
        </svg>
    </span>
</th>
