<?php

namespace Tests\Feature\Livewire;

use App\Livewire\DestinationExplorer;
use App\Models\Destination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class DestinationExplorerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_destinations_from_the_database_sorted_by_name(): void
    {
        $this->createNamed('Kyoto', 'Bali', 'Cusco');

        $this->explorer()->assertSeeInOrder(['Bali', 'Cusco', 'Kyoto']);
    }

    public function test_searching_narrows_the_list(): void
    {
        $this->createNamed('Kyoto', 'Bali');

        $this->explorer()
            ->set('search', 'kyo')
            ->assertSee('Kyoto')
            ->assertDontSee('Bali');
    }

    public function test_search_ignores_case_and_surrounding_spaces(): void
    {
        $this->createNamed('Kyoto', 'Bali');

        $this->explorer()
            ->set('search', '  KYOTO ')
            ->assertSee('Kyoto')
            ->assertDontSee('Bali');
    }

    public function test_zero_is_searched_for_and_not_treated_as_empty(): void
    {
        $this->createNamed('Route 0', 'Bali');

        $this->explorer()
            ->set('search', '0')
            ->assertSee('Route 0')
            ->assertDontSee('Bali');
    }

    public function test_sorting_by_the_current_column_flips_the_direction(): void
    {
        $this->createNamed('Bali', 'Cusco', 'Kyoto');

        $explorer = $this->explorer();

        $explorer
            ->call('sortBy', 'name')
            ->assertSet('direction', 'desc')
            ->assertSeeInOrder(['Kyoto', 'Cusco', 'Bali']);

        $explorer
            ->call('sortBy', 'name')
            ->assertSet('direction', 'asc')
            ->assertSeeInOrder(['Bali', 'Cusco', 'Kyoto']);
    }

    public function test_sorting_by_another_column_starts_ascending(): void
    {
        Destination::factory()->create(['name' => 'Costly', 'average_daily_budget' => 400]);
        Destination::factory()->create(['name' => 'Cheap', 'average_daily_budget' => 50]);

        $this->explorer()
            ->call('sortBy', 'name')
            ->call('sortBy', 'averageDailyBudget')
            ->assertSet('sort', 'averageDailyBudget')
            ->assertSet('direction', 'asc')
            ->assertSeeInOrder(['Cheap', 'Costly']);
    }

    public function test_an_unknown_sort_field_is_ignored(): void
    {
        $this->createNamed('Bali', 'Kyoto');

        $this->explorer()
            ->call('sortBy', 'password')
            ->assertSet('sort', 'name')
            ->assertSet('direction', 'asc')
            ->assertSeeInOrder(['Bali', 'Kyoto']);
    }

    public function test_tampered_sort_state_falls_back_to_the_default_order(): void
    {
        $this->createNamed('Kyoto', 'Bali');

        $this->explorer()
            ->set('sort', 'password')
            ->set('direction', 'sideways')
            ->assertSeeInOrder(['Bali', 'Kyoto']);
    }

    public function test_searching_goes_back_to_the_first_page(): void
    {
        Destination::factory()->count(20)->create();

        $this->explorer()
            ->call('gotoPage', 2)
            ->assertSet('paginators.page', 2)
            ->set('search', 'a')
            ->assertSet('paginators.page', 1);
    }

    public function test_sorting_goes_back_to_the_first_page(): void
    {
        Destination::factory()->count(20)->create();

        $this->explorer()
            ->call('gotoPage', 2)
            ->call('sortBy', 'country')
            ->assertSet('paginators.page', 1);
    }

    public function test_resetting_clears_the_search_and_keeps_the_sorting(): void
    {
        Destination::factory()->count(20)->create();

        $this->explorer()
            ->call('sortBy', 'country')
            ->set('search', 'a')
            ->call('gotoPage', 2)
            ->call('resetSearch')
            ->assertSet('search', '')
            ->assertSet('sort', 'country')
            ->assertSet('direction', 'asc')
            ->assertSet('paginators.page', 1);
    }

    public function test_search_sorting_and_page_are_restored_from_the_url(): void
    {
        Destination::factory()->count(12)->create(['region' => 'Europe']);
        Destination::factory()->create(['name' => 'Bali', 'region' => 'Asia']);

        $this->explorer(['search' => 'europe', 'sort' => 'country', 'direction' => 'desc', 'page' => 2])
            ->assertSet('search', 'europe')
            ->assertSet('sort', 'country')
            ->assertSet('direction', 'desc')
            ->assertSet('paginators.page', 2)
            ->assertDontSee('Bali');
    }

    public function test_invalid_url_values_fall_back_to_the_default_order(): void
    {
        $this->createNamed('Kyoto', 'Bali');

        $this->explorer(['sort' => 'password', 'direction' => 'sideways'])
            ->assertSeeInOrder(['Bali', 'Kyoto']);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return Testable<Component>
     */
    private function explorer(array $query = []): Testable
    {
        return Livewire::withQueryParams($query)->test(DestinationExplorer::class);
    }

    private function createNamed(string ...$names): void
    {
        foreach ($names as $name) {
            Destination::factory()->create(['name' => $name, 'country' => 'Nowhere', 'region' => 'Unknown']);
        }
    }
}
