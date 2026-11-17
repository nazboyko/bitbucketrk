<?php

namespace App\Http\Resources;

use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Destination
 */
class DestinationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'country' => $this->country,
            'region' => $this->region,
            'costLevel' => $this->cost_level,
            'activities' => $this->activities,
            'averageDailyBudget' => $this->average_daily_budget,
            'annualVisitors' => $this->annual_visitors,
        ];
    }
}
