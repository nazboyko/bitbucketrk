<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private const int API_REQUESTS_PER_MINUTE = 60;

    public function boot(): void
    {
        RateLimiter::for('api', fn (Request $request): Limit => Limit::perMinute(self::API_REQUESTS_PER_MINUTE)
            ->by($request->user()?->getAuthIdentifier() ?? $request->ip()));
    }
}
