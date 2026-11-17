<?php

namespace Database\Factories;

use App\Enums\CostLevel;
use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Destination>
 */
class DestinationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city(),
            'country' => fake()->country(),
            'region' => fake()->randomElement(['Africa', 'Asia', 'Europe', 'North America', 'Oceania', 'South America']),
            'cost_level' => fake()->randomElement(CostLevel::cases()),
            'activities' => fake()->randomElements(['Camping', 'Cultural Tours', 'Photography', 'Surfing'], 2),
            'average_daily_budget' => fake()->numberBetween(40, 500),
            'annual_visitors' => fake()->numberBetween(100_000, 10_000_000),
        ];
    }
}
