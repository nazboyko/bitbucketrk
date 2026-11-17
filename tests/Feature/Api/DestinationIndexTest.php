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

        $this->getJson('/api/destinations')
            ->assertOk()
            ->assertExactJson([
                'data' => [
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
                ],
            ]);
    }

    public function test_it_returns_an_empty_list_when_there_are_no_destinations(): void
    {
        $this->getJson('/api/destinations')
            ->assertOk()
            ->assertExactJson(['data' => []]);
    }
}
