<?php

declare(strict_types=1);

namespace App\Concerns;

trait HasIntervalAttribute
{
    public function initializeHasIntervalAttribute(): void
    {
        $this->fillable = array_merge($this->fillable, ['start_date_interval', 'end_date_interval']);
    }

    public function getIntervalAttribute(): string
    {
        return $this->start_date_interval . ' - ' . $this->end_date_interval;
    }
}
