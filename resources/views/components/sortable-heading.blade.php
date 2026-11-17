@use('App\Enums\SortDirection')

@props(['option', 'current', 'direction'])

@php
    $active = $option === $current;
@endphp

<th
    scope="col"
    @if ($active) aria-sort="{{ strtolower($direction->name) }}" @endif
    {{ $attributes->class('px-4 py-3 font-medium') }}
>
    <button
        type="button"
        wire:click="sortBy('{{ $option->value }}')"
        class="inline-flex items-center gap-1 rounded hover:text-gray-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
    >
        {{ $slot }}

        <svg
            viewBox="0 0 12 12"
            fill="currentColor"
            aria-hidden="true"
            @class([
                'size-3',
                'invisible' => ! $active,
                'rotate-180' => $direction === SortDirection::Descending,
            ])
        >
            <path d="M6 3l4 6H2z" />
        </svg>
    </button>
</th>
