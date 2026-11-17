<?php

namespace Tests\Feature\Api;

use App\Enums\TokenAbility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create(), [TokenAbility::ReadDestinations->value]);
    }

    public function test_a_client_is_slowed_down_after_sixty_requests_in_a_minute(): void
    {
        for ($request = 1; $request <= 60; $request++) {
            $this->get('/api/destinations')->assertOk();
        }

        $this->get('/api/destinations')
            ->assertTooManyRequests()
            ->assertJsonStructure(['message']);
    }
}
