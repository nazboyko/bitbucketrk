<?php

namespace App\Livewire;

use App\Enums\DestinationSort;
use App\Enums\SortDirection;
use App\Models\Destination;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class DestinationExplorer extends Component
{
    use WithPagination;

    private const int PER_PAGE = 10;

    public string $search = '';

    public string $sort = DestinationSort::Name->value;

    public string $direction = SortDirection::Ascending->value;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        $sort = DestinationSort::tryFrom($field);

        if ($sort === null) {
            return;
        }

        $direction = $sort === $this->sortOption()
            ? $this->sortDirection()->opposite()
            : SortDirection::Ascending;

        $this->sort = $sort->value;
        $this->direction = $direction->value;
        $this->resetPage();
    }

    public function resetSearch(): void
    {
        $this->reset('search');
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.destination-explorer', [
            'destinations' => Destination::query()
                ->search($this->search)
                ->sortedBy($this->sortOption(), $this->sortDirection())
                ->paginate(self::PER_PAGE),
        ]);
    }

    /**
     * The browser can send any value for a public property, so unknown ones fall back to the default.
     */
    private function sortOption(): DestinationSort
    {
        return DestinationSort::tryFrom($this->sort) ?? DestinationSort::Name;
    }

    private function sortDirection(): SortDirection
    {
        return SortDirection::tryFrom($this->direction) ?? SortDirection::Ascending;
    }
}
