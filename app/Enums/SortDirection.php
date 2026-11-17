<?php

namespace App\Enums;

enum SortDirection: string
{
    case Ascending = 'asc';
    case Descending = 'desc';

    public function opposite(): self
    {
        return $this === self::Ascending ? self::Descending : self::Ascending;
    }
}
