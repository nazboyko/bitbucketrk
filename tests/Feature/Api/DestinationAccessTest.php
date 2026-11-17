<?php

namespace Tests\Feature\Api;

use App\Enums\TokenAbility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestinationAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_request_without_a_token_is_rejected(): void
    {
        $this->get('/api/destinations')
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    public function test_a_made_up_token_is_rejected(): void
    {
        $this->withToken('not-a-real-token')
            ->get('/api/destinations')
            ->assertUnauthorized();
    }

    public function test_a_token_without_the_read_ability_is_forbidden(): void
    {
        $token = User::factory()->create()->createToken('test', ['something:else']);

        $this->withToken($token->plainTextToken)
            ->get('/api/destinations')
            ->assertForbidden();
    }

    public function test_a_token_with_the_read_ability_is_accepted(): void
    {
        $token = User::factory()->create()->createToken('test', [TokenAbility::ReadDestinations->value]);

        $this->withToken($token->plainTextToken)
            ->get('/api/destinations')
            ->assertOk();
    }

    public function test_the_destinations_page_stays_public(): void
    {
        $this->withoutVite();

        $this->get('/')->assertOk();
    }
}
