<?php

namespace Tests\Feature;

use Tests\TestCase;

class SmokeTest extends TestCase
{
    public function test_home_page_loads(): void
    {
        $this->withoutVite();

        $this->get('/')->assertOk();
    }

    public function test_health_route_responds(): void
    {
        $this->get('/up')->assertOk();
    }
}
