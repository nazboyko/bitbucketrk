<?php

namespace App\Models;

use App\Enums\CostLevel;
use Database\Factories\DestinationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'country', 'region', 'cost_level', 'activities', 'average_daily_budget', 'annual_visitors'])]
class Destination extends Model
{
    /** @use HasFactory<DestinationFactory> */
    use HasFactory;

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
}
