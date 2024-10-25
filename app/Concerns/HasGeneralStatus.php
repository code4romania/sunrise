<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Enums\GeneralStatus;
use Illuminate\Database\Eloquent\Builder;

trait HasGeneralStatus
{
    public function initializeHasGeneralStatus(): void
    {
        $this->fillable = array_merge($this->fillable, ['status']);
        $this->casts['status'] = GeneralStatus::class;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', GeneralStatus::ACTIVE);
    }
}
