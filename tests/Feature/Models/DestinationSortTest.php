<?php

namespace Tests\Feature\Models;

use App\Enums\CostLevel;
use App\Enums\DestinationSort;
use App\Enums\SortDirection;
use App\Models\Destination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestinationSortTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sorts_by_a_column_in_both_directions(): void
    {
        Destination::factory()->create(['name' => 'Bali']);
        Destination::factory()->create(['name' => 'Kyoto']);
        Destination::factory()->create(['name' => 'Cusco']);

        $this->assertSame(['Bali', 'Cusco', 'Kyoto'], $this->sorted(DestinationSort::Name, SortDirection::Ascending, 'name'));
        $this->assertSame(['Kyoto', 'Cusco', 'Bali'], $this->sorted(DestinationSort::Name, SortDirection::Descending, 'name'));
    }

    public function test_numbers_are_sorted_as_numbers(): void
    {
        Destination::factory()->create(['average_daily_budget' => 100]);
        Destination::factory()->create(['average_daily_budget' => 60]);
        Destination::factory()->create(['average_daily_budget' => 250]);

        $this->assertSame(
            [60, 100, 250],
            $this->sorted(DestinationSort::AverageDailyBudget, SortDirection::Ascending, 'average_daily_budget'),
        );
    }

    public function test_cost_level_is_sorted_from_cheapest_to_most_expensive(): void
    {
        foreach ([CostLevel::Premium, CostLevel::Budget, CostLevel::Luxury, CostLevel::Moderate] as $level) {
            Destination::factory()->create(['cost_level' => $level]);
        }

        $levels = array_column(CostLevel::cases(), 'value');

        $this->assertSame($levels, $this->sorted(DestinationSort::CostLevel, SortDirection::Ascending, 'cost_level'));
        $this->assertSame(array_reverse($levels), $this->sorted(DestinationSort::CostLevel, SortDirection::Descending, 'cost_level'));
    }

    public function test_equal_values_keep_the_order_they_were_created_in(): void
    {
        $first = Destination::factory()->create(['region' => 'Europe']);
        $second = Destination::factory()->create(['region' => 'Europe']);
        $third = Destination::factory()->create(['region' => 'Europe']);

        $expected = [$first->id, $second->id, $third->id];

        $this->assertSame($expected, $this->sorted(DestinationSort::Region, SortDirection::Ascending, 'id'));
        $this->assertSame($expected, $this->sorted(DestinationSort::Region, SortDirection::Descending, 'id'));
    }

    /**
     * The raw values of one column, in the order the scope returns them.
     *
     * @return array<mixed>
     */
    private function sorted(DestinationSort $sort, SortDirection $direction, string $column): array
    {
        return Destination::query()->sortedBy($sort, $direction)->toBase()->pluck($column)->all();
    }
}
