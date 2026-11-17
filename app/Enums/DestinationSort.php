<?php

namespace App\Enums;

use Illuminate\Support\Str;

/**
 * The fields a destination list can be sorted by, named as the API exposes them.
 */
enum DestinationSort: string
{
    case Name = 'name';
    case Country = 'country';
    case Region = 'region';
    case CostLevel = 'costLevel';
    case AverageDailyBudget = 'averageDailyBudget';
    case AnnualVisitors = 'annualVisitors';

    public function column(): string
    {
        return Str::snake($this->value);
    }
}
