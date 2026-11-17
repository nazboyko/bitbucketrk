<?php

namespace App\Models;

use App\Enums\CostLevel;
use App\Enums\DestinationSort;
use App\Enums\SortDirection;
use Database\Factories\DestinationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $country
 * @property string $region
 * @property CostLevel $cost_level
 * @property list<string> $activities
 * @property int $average_daily_budget
 * @property int $annual_visitors
 */
#[Fillable(['name', 'country', 'region', 'cost_level', 'activities', 'average_daily_budget', 'annual_visitors'])]
class Destination extends Model
{
    /** @use HasFactory<DestinationFactory> */
    use HasFactory;

    public const int SEARCH_MAX_LENGTH = 100;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cost_level' => CostLevel::class,
            'activities' => 'array',
            'average_daily_budget' => 'integer',
            'annual_visitors' => 'integer',
        ];
    }

    /**
     * Match the term against the text fields and activity names. A whole number also matches the daily budget.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function search(Builder $query, ?string $term): void
    {
        $term = mb_substr(trim((string) $term), 0, self::SEARCH_MAX_LENGTH);

        if ($term === '') {
            return;
        }

        $pattern = '%'.addcslashes($term, '%_\\').'%';

        $query->where(function (Builder $query) use ($term, $pattern): void {
            $query
                ->whereLike('name', $pattern)
                ->orWhereLike('country', $pattern)
                ->orWhereLike('region', $pattern)
                ->orWhereLike('cost_level', $pattern)
                // JSON text is compared byte by byte, so both sides are lowercased here.
                ->orWhereRaw("json_search(lower(activities), 'one', ?) is not null", [mb_strtolower($pattern)]);

            if (ctype_digit($term)) {
                $query->orWhere('average_daily_budget', (int) $term);
            }
        });
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function sortedBy(Builder $query, DestinationSort $sort, SortDirection $direction): void
    {
        if ($sort === DestinationSort::CostLevel) {
            $levels = array_column(CostLevel::cases(), 'value');
            $placeholders = implode(', ', array_fill(0, count($levels), '?'));

            $query->orderByRaw("field(cost_level, {$placeholders}) {$direction->value}", $levels);
        } else {
            $query->orderBy($sort->column(), $direction->value);
        }

        $query->orderBy('id');
    }
}
