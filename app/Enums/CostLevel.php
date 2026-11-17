<?php

namespace App\Enums;

/**
 * Cases are listed from cheapest to most expensive. Sorting relies on this order.
 */
enum CostLevel: string
{
    case Budget = 'Budget';
    case Moderate = 'Moderate';
    case Premium = 'Premium';
    case Luxury = 'Luxury';
}
