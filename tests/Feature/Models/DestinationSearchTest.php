<?php

namespace Tests\Feature\Models;

use App\Enums\CostLevel;
use App\Models\Destination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DestinationSearchTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('matchingTerms')]
    public function test_it_finds_a_destination_by_each_searchable_field(string $term): void
    {
        $match = $this->destination([
            'name' => 'Machu Picchu',
            'country' => 'Peru',
            'region' => 'South America',
            'cost_level' => CostLevel::Luxury,
            'activities' => ['Hiking & Trekking', 'Photography'],
            'average_daily_budget' => 150,
        ]);
        $this->destination();

        $this->assertSame([$match->id], $this->search($term));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function matchingTerms(): array
    {
        return [
            'name' => ['Picchu'],
            'country' => ['Peru'],
            'region' => ['South'],
            'cost level' => ['Luxury'],
            'activity' => ['Trekking'],
            'activity with a symbol' => ['Hiking & Trekking'],
            'exact budget' => ['150'],
            'lower case' => ['peru'],
            'upper case' => ['PERU'],
            'lower case activity' => ['photography'],
            'surrounding spaces' => ['  Peru  '],
        ];
    }

    public function test_a_blank_term_returns_every_destination(): void
    {
        $this->destination();
        $this->destination();

        $this->assertCount(2, $this->search(''));
        $this->assertCount(2, $this->search('   '));
        $this->assertCount(2, $this->search(null));
    }

    public function test_zero_is_a_real_search_term(): void
    {
        $match = $this->destination(['name' => 'Route 0']);
        $this->destination();

        $this->assertSame([$match->id], $this->search('0'));
    }

    public function test_a_number_matches_the_budget_exactly_and_not_in_part(): void
    {
        $this->destination(['average_daily_budget' => 1500]);

        $this->assertSame([], $this->search('150'));
    }

    public function test_wildcard_characters_are_plain_text(): void
    {
        $underscore = $this->destination(['name' => 'Snake_Case']);
        $percent = $this->destination(['name' => 'Half 50% Off']);
        $this->destination(['name' => 'SnakeXCase']);

        $this->assertSame([$underscore->id], $this->search('_'));
        $this->assertSame([$percent->id], $this->search('%'));
    }

    public function test_json_punctuation_does_not_match_activities(): void
    {
        $this->destination(['activities' => ['Surfing', 'Camping']]);

        $this->assertSame([], $this->search('"'));
        $this->assertSame([], $this->search('['));
        $this->assertSame([], $this->search(','));
    }

    /**
     * A destination whose text does not match any term used in these tests.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function destination(array $attributes = []): Destination
    {
        return Destination::factory()->create($attributes + [
            'name' => 'Elsewhere '.fake()->unique()->lexify('??????'),
            'country' => 'Nowhere',
            'region' => 'Unknown',
            'cost_level' => CostLevel::Budget,
            'activities' => ['Resting'],
            'average_daily_budget' => 99,
        ]);
    }

    /**
     * @return array<mixed>
     */
    private function search(?string $term): array
    {
        return Destination::query()->search($term)->orderBy('id')->pluck('id')->all();
    }
}
