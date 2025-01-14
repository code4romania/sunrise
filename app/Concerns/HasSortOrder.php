<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Scopes\SortOrder;

trait HasSortOrder
{
    public function initializeHasSortOrder(): void
    {
        $this->fillable[] = 'sort';
    }

    protected static function bootHasSortOrder(): void
    {
        static::addGlobalScope(new SortOrder);
    }
}
