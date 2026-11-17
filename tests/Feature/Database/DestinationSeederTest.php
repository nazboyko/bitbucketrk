<?php

namespace Tests\Feature\Database;

use App\Models\Destination;
use Database\Seeders\DestinationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestinationSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeding_twice_does_not_duplicate_destinations(): void
    {
        $this->seed(DestinationSeeder::class);
        $this->seed(DestinationSeeder::class);

        $this->assertDatabaseCount('destinations', 15);
    }

    public function test_seeding_keeps_destinations_it_did_not_create(): void
    {
        $custom = Destination::factory()->create(['name' => 'Austin', 'country' => 'US']);

        $this->seed(DestinationSeeder::class);

        $this->assertModelExists($custom);
        $this->assertDatabaseCount('destinations', 16);
    }
}
