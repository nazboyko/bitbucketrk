<?php

namespace Database\Seeders;

use App\Enums\CostLevel;
use App\Models\Destination;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    /**
     * Safe to run more than once: demo rows are matched by name and country.
     */
    public function run(): void
    {
        foreach ($this->destinations() as $destination) {
            Destination::updateOrCreate(
                ['name' => $destination['name'], 'country' => $destination['country']],
                $destination,
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function destinations(): array
    {
        return [
            [
                'name' => 'Machu Picchu',
                'country' => 'Peru',
                'region' => 'South America',
                'cost_level' => CostLevel::Moderate,
                'activities' => ['Hiking & Trekking', 'Cultural Tours', 'Photography', 'Historical Sightseeing'],
                'average_daily_budget' => 150,
                'annual_visitors' => 1_500_000,
            ],
            [
                'name' => 'Santorini',
                'country' => 'Greece',
                'region' => 'Europe',
                'cost_level' => CostLevel::Premium,
                'activities' => ['Beach & Relaxation', 'Food & Wine Tasting', 'Photography', 'Sailing & Boating'],
                'average_daily_budget' => 250,
                'annual_visitors' => 2_000_000,
            ],
            [
                'name' => 'Kyoto',
                'country' => 'Japan',
                'region' => 'Asia',
                'cost_level' => CostLevel::Moderate,
                'activities' => ['Cultural Tours', 'Historical Sightseeing', 'Food & Wine Tasting', 'Photography'],
                'average_daily_budget' => 180,
                'annual_visitors' => 5_300_000,
            ],
            [
                'name' => 'Serengeti National Park',
                'country' => 'Tanzania',
                'region' => 'Africa',
                'cost_level' => CostLevel::Premium,
                'activities' => ['Wildlife Safari', 'Photography', 'Hot Air Ballooning', 'Camping'],
                'average_daily_budget' => 300,
                'annual_visitors' => 350_000,
            ],
            [
                'name' => 'Queenstown',
                'country' => 'New Zealand',
                'region' => 'Oceania',
                'cost_level' => CostLevel::Premium,
                'activities' => ['Adventure Sports', 'Skiing & Snowboarding', 'Hiking & Trekking', 'Paragliding'],
                'average_daily_budget' => 200,
                'annual_visitors' => 3_000_000,
            ],
            [
                'name' => 'Reykjavik',
                'country' => 'Iceland',
                'region' => 'Europe',
                'cost_level' => CostLevel::Premium,
                'activities' => ['Hiking & Trekking', 'Hot Air Ballooning', 'Photography', 'Volcano Tours'],
                'average_daily_budget' => 280,
                'annual_visitors' => 2_300_000,
            ],
            [
                'name' => 'Cusco',
                'country' => 'Peru',
                'region' => 'South America',
                'cost_level' => CostLevel::Budget,
                'activities' => ['Hiking & Trekking', 'Cultural Tours', 'Historical Sightseeing', 'Food & Wine Tasting'],
                'average_daily_budget' => 60,
                'annual_visitors' => 2_700_000,
            ],
            [
                'name' => 'Bali',
                'country' => 'Indonesia',
                'region' => 'Asia',
                'cost_level' => CostLevel::Budget,
                'activities' => ['Beach & Relaxation', 'Surfing', 'Yoga & Wellness Retreats', 'Cultural Tours'],
                'average_daily_budget' => 70,
                'annual_visitors' => 6_200_000,
            ],
            [
                'name' => 'Banff National Park',
                'country' => 'Canada',
                'region' => 'North America',
                'cost_level' => CostLevel::Moderate,
                'activities' => ['Hiking & Trekking', 'Skiing & Snowboarding', 'Kayaking & Canoeing', 'Wildlife Safari'],
                'average_daily_budget' => 180,
                'annual_visitors' => 4_000_000,
            ],
            [
                'name' => 'Patagonia',
                'country' => 'Argentina',
                'region' => 'South America',
                'cost_level' => CostLevel::Premium,
                'activities' => ['Hiking & Trekking', 'Rock Climbing', 'Photography', 'Camping'],
                'average_daily_budget' => 220,
                'annual_visitors' => 400_000,
            ],
            [
                'name' => 'Marrakech',
                'country' => 'Morocco',
                'region' => 'Africa',
                'cost_level' => CostLevel::Budget,
                'activities' => ['Cultural Tours', 'Food & Wine Tasting', 'Historical Sightseeing', 'Nightlife & Entertainment'],
                'average_daily_budget' => 55,
                'annual_visitors' => 3_000_000,
            ],
            [
                'name' => 'Dubrovnik',
                'country' => 'Croatia',
                'region' => 'Europe',
                'cost_level' => CostLevel::Moderate,
                'activities' => ['Historical Sightseeing', 'Beach & Relaxation', 'Kayaking & Canoeing', 'Food & Wine Tasting'],
                'average_daily_budget' => 160,
                'annual_visitors' => 1_400_000,
            ],
            [
                'name' => 'Cancun',
                'country' => 'Mexico',
                'region' => 'North America',
                'cost_level' => CostLevel::Moderate,
                'activities' => ['Beach & Relaxation', 'Scuba Diving & Snorkeling', 'Nightlife & Entertainment', 'Cultural Tours'],
                'average_daily_budget' => 140,
                'annual_visitors' => 8_000_000,
            ],
            [
                'name' => 'Phuket',
                'country' => 'Thailand',
                'region' => 'Asia',
                'cost_level' => CostLevel::Budget,
                'activities' => ['Beach & Relaxation', 'Scuba Diving & Snorkeling', 'Food & Wine Tasting', 'Nightlife & Entertainment'],
                'average_daily_budget' => 80,
                'annual_visitors' => 9_500_000,
            ],
            [
                'name' => 'Swiss Alps',
                'country' => 'Switzerland',
                'region' => 'Europe',
                'cost_level' => CostLevel::Luxury,
                'activities' => ['Skiing & Snowboarding', 'Hiking & Trekking', 'Rock Climbing', 'Photography'],
                'average_daily_budget' => 400,
                'annual_visitors' => 1_200_000,
            ],
        ];
    }
}
