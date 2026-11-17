@use('App\Enums\DestinationSort')
@use('App\Models\Destination')

<div>
    <h1 class="text-2xl font-semibold">Destinations</h1>

    <div class="mt-6">
        <label for="destination-search" class="block text-sm font-medium text-gray-700">Search destinations</label>

        <div class="mt-1 flex gap-3">
            <input
                type="search"
                id="destination-search"
                wire:model.live.debounce.300ms="search"
                maxlength="{{ Destination::SEARCH_MAX_LENGTH }}"
                placeholder="Name, country, region, cost level or activity"
                class="w-full max-w-sm rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
            >

            <button
                type="button"
                wire:click="resetSearch"
                class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
            >
                Reset
            </button>
        </div>
    </div>

    <p role="status" class="mt-6 flex h-5 items-center gap-2 text-sm text-gray-600">
        <span>{{ $destinations->total() }} {{ Str::plural('destination', $destinations->total()) }}</span>
        <span wire:loading.delay class="text-gray-400">Updating</span>
    </p>

    <div
        role="region"
        aria-label="Destinations table"
        tabindex="0"
        class="mt-2 overflow-x-auto rounded-lg border border-gray-200 bg-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
    >
        <table class="w-full text-left text-sm transition-opacity" wire:loading.delay.class="opacity-50">
            <caption class="sr-only">Travel destinations</caption>
            <thead class="border-b border-gray-200 bg-gray-100 text-gray-600">
            <tr>
                <x-sortable-heading :option="DestinationSort::Name" :current="$currentSort" :direction="$currentDirection">
                    Name
                </x-sortable-heading>
                <x-sortable-heading :option="DestinationSort::Country" :current="$currentSort" :direction="$currentDirection">
                    Country
                </x-sortable-heading>
                <x-sortable-heading :option="DestinationSort::Region" :current="$currentSort" :direction="$currentDirection">
                    Region
                </x-sortable-heading>
                <x-sortable-heading :option="DestinationSort::CostLevel" :current="$currentSort" :direction="$currentDirection">
                    Cost level
                </x-sortable-heading>
                <th scope="col" class="px-4 py-3 font-medium">Activities</th>
                <x-sortable-heading :option="DestinationSort::AverageDailyBudget" :current="$currentSort" :direction="$currentDirection" class="text-right">
                    Daily budget
                </x-sortable-heading>
                <x-sortable-heading :option="DestinationSort::AnnualVisitors" :current="$currentSort" :direction="$currentDirection" class="text-right">
                    Annual visitors
                </x-sortable-heading>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            @forelse ($destinations as $destination)
                <tr wire:key="{{ $destination->id }}" class="hover:bg-gray-50">
                    <th scope="row" class="px-4 py-3 font-medium">{{ $destination->name }}</th>
                    <td class="px-4 py-3">{{ $destination->country }}</td>
                    <td class="px-4 py-3">{{ $destination->region }}</td>
                    <td class="px-4 py-3">{{ $destination->cost_level->value }}</td>
                    <td class="px-4 py-3">
                        <ul class="flex min-w-56 flex-wrap gap-1">
                            @foreach ($destination->activities as $activity)
                                <li class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ $activity }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="px-4 py-3 text-right tabular-nums">${{ number_format($destination->average_daily_budget) }}</td>
                    <td class="px-4 py-3 text-right tabular-nums">{{ number_format($destination->annual_visitors) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-600">
                        @if ($search === '')
                            There are no destinations yet.
                        @else
                            No destinations match your search.

                            <button
                                type="button"
                                wire:click="resetSearch"
                                class="rounded font-medium text-blue-700 underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                            >
                                Clear search
                            </button>
                        @endif
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $destinations->links() }}
    </div>
</div>
