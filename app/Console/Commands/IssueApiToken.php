<?php

namespace App\Console\Commands;

use App\Enums\TokenAbility;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

#[Signature('api:token {email : Address of the user who will own the token}')]
#[Description('Issue an API token that can read destinations')]
class IssueApiToken extends Command
{
    public function handle(): int
    {
        $email = $this->argument('email');

        if (Validator::make(['email' => $email], ['email' => 'email'])->fails()) {
            $this->error("\"{$email}\" is not a valid email address.");

            return self::FAILURE;
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => Str::before($email, '@'), 'password' => Str::password()],
        );

        $token = $user->createToken('api', [TokenAbility::ReadDestinations->value]);

        $this->info("Token for {$email}. It is shown only once:");
        $this->line($token->plainTextToken);

        return self::SUCCESS;
    }
}
