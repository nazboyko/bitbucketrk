<?php

namespace Tests\Feature\Console;

use App\Enums\TokenAbility;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class IssueApiTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_printed_token_opens_the_api(): void
    {
        $this->issueToken('client@example.com');

        $token = trim(str(Artisan::output())->trim()->afterLast("\n"));

        $this->withToken($token)->get('/api/destinations')->assertOk();
    }

    public function test_it_creates_the_user_and_limits_the_token_to_reading(): void
    {
        $this->assertSame(Command::SUCCESS, $this->issueToken('client@example.com'));

        $user = User::where('email', 'client@example.com')->sole();

        $this->assertSame([TokenAbility::ReadDestinations->value], $user->tokens()->sole()->abilities);
    }

    public function test_it_adds_a_token_to_an_existing_user(): void
    {
        $user = User::factory()->create(['email' => 'client@example.com']);

        $this->assertSame(Command::SUCCESS, $this->issueToken('client@example.com'));

        $this->assertDatabaseCount('users', 1);
        $this->assertCount(1, $user->tokens);
    }

    public function test_it_refuses_an_invalid_email_address(): void
    {
        $this->assertSame(Command::FAILURE, $this->issueToken('not-an-email'));

        $this->assertDatabaseCount('users', 0);
    }

    private function issueToken(string $email): int
    {
        return Artisan::call('api:token', ['email' => $email]);
    }
}
