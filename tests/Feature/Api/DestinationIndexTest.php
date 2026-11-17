<?php

namespace Tests\Feature\Api;

use App\Enums\CostLevel;
use App\Models\Destination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestinationIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_destinations_stored_in_the_database(): void
    {
        $destination = Destination::factory()->create([
            'name' => 'Lviv',
            'country' => 'Ukraine',
            'region' => 'Europe',
            'cost_level' => CostLevel::Budget,
            'activities' => ['Cultural Tours', 'Food & Wine Tasting'],
            'average_daily_budget' => 45,
            'annual_visitors' => 2_500_000,
        ]);

        $this->get('/api/destinations')
            ->assertOk()
            ->assertExactJsonStructure(['data', 'links', 'meta'])
            ->assertJsonPath('data', [
                [
                    'id' => $destination->id,
                    'name' => 'Lviv',
                    'country' => 'Ukraine',
                    'region' => 'Europe',
                    'costLevel' => 'Budget',
                    'activities' => ['Cultural Tours', 'Food & Wine Tasting'],
                    'averageDailyBudget' => 45,
                    'annualVisitors' => 2_500_000,
                ],
            ]);
    }

    public function test_it_returns_an_empty_list_when_there_are_no_destinations(): void
    {
        $this->get('/api/destinations')
            ->assertOk()
            ->assertJsonPath('data', [])
            ->assertJsonPath('meta.total', 0);
    }

    public function test_it_sorts_by_name_when_no_sort_is_given(): void
    {
        $this->createNamed('Kyoto', 'Bali', 'Cusco');

        $this->get('/api/destinations')->assertJsonPath('data.*.name', ['Bali', 'Cusco', 'Kyoto']);
    }

    public function test_it_filters_by_the_search_term(): void
    {
        $this->createNamed('Kyoto', 'Bali');

        $this->get('/api/destinations?search=kyo')->assertJsonPath('data.*.name', ['Kyoto']);
    }

    public function test_it_sorts_by_the_requested_field_and_direction(): void
    {
        Destination::factory()->create(['name' => 'Cheap', 'average_daily_budget' => 50]);
        Destination::factory()->create(['name' => 'Costly', 'average_daily_budget' => 400]);

        $this->get('/api/destinations?sort=averageDailyBudget&direction=desc')
            ->assertJsonPath('data.*.name', ['Costly', 'Cheap']);
    }

    public function test_it_splits_the_list_into_pages(): void
    {
        $this->createNamed('Bali', 'Cusco', 'Kyoto');

        $this->get('/api/destinations?per_page=2&page=2')
            ->assertJsonPath('data.*.name', ['Kyoto'])
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 3);
    }

    public function test_page_links_keep_the_other_query_parameters(): void
    {
        $this->createNamed('Bali', 'Bari', 'Baku');

        $next = $this->get('/api/destinations?search=ba&sort=name&per_page=1')->json('links.next');

        $this->assertStringContainsString('search=ba', $next);
        $this->assertStringContainsString('per_page=1', $next);
        $this->assertStringContainsString('page=2', $next);
    }

    #[DataProvider('invalidQueries')]
    public function test_it_rejects_invalid_parameters_with_a_json_error(string $query, string $field): void
    {
        $this->get("/api/destinations?{$query}")
            ->assertUnprocessable()
            ->assertJsonValidationErrors($field);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function invalidQueries(): array
    {
        return [
            'unknown sort field' => ['sort=password', 'sort'],
            'database column as sort field' => ['sort=cost_level', 'sort'],
            'unknown direction' => ['direction=sideways', 'direction'],
            'page below one' => ['page=0', 'page'],
            'page that is not a number' => ['page=first', 'page'],
            'page size of zero' => ['per_page=0', 'per_page'],
            'page size above the limit' => ['per_page=101', 'per_page'],
            'search term that is too long' => ['search='.str_repeat('a', 101), 'search'],
        ];
    }

    private function createNamed(string ...$names): void
    {
        foreach ($names as $name) {
            Destination::factory()->create(['name' => $name, 'country' => 'Nowhere', 'region' => 'Unknown']);
        }
    }
}
