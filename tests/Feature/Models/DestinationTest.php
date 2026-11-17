<?php

namespace Tests\Feature\Models;

use App\Enums\CostLevel;
use App\Models\Destination;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestinationTest extends TestCase
{
    use RefreshDatabase;

    public function test_stored_values_come_back_with_their_types(): void
    {
        $destination = Destination::factory()->create([
            'cost_level' => CostLevel::Premium,
            'activities' => ['Surfing', 'Camping'],
            'average_daily_budget' => 120,
            'annual_visitors' => 2_500_000,
        ])->refresh();

        $this->assertSame(CostLevel::Premium, $destination->cost_level);
        $this->assertSame(['Surfing', 'Camping'], $destination->activities);
        $this->assertSame(120, $destination->average_daily_budget);
        $this->assertSame(2_500_000, $destination->annual_visitors);
    }

    public function test_a_destination_is_unique_within_its_country(): void
    {
        Destination::factory()->create(['name' => 'Springfield', 'country' => 'USA']);
        Destination::factory()->create(['name' => 'Springfield', 'country' => 'Canada']);

        $this->expectException(UniqueConstraintViolationException::class);

        Destination::factory()->create(['name' => 'Springfield', 'country' => 'USA']);
    }
}
