<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Enums\CaseStatus;
use Illuminate\Database\Eloquent\Builder;

trait HasCaseStatus
{
    public function initializeHasCaseStatus()
    {
        $this->casts['status'] = CaseStatus::class;

        $this->fillable[] = 'status';
    }

    public function scopeWhereCaseIsActive(Builder $builder): Builder
    {
        return $builder->where('status', CaseStatus::ACTIVE);
    }

    public function scopeWhereCaseIsReactivated(Builder $builder): Builder
    {
        return $builder->whereNotNull('initial_id');
    }

    public function scopeWhereCaseIsMonitored(Builder $builder): Builder
    {
        return $builder->where('status', CaseStatus::MONITORED);
    }

    public function scopeWhereCaseIsClosed(Builder $builder): Builder
    {
        return $builder->where('status', CaseStatus::CLOSED);
    }
}
