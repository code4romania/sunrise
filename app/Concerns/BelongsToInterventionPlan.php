<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Beneficiary;
use App\Models\InterventionPlan;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;

trait BelongsToInterventionPlan
{
    use BelongsToThroughTrait;

    public function initializeBelongsToInterventionPlan(): void
    {
        $this->fillable[] = 'intervention_plan_id';
    }

    public function interventionPlan(): BelongsTo
    {
        return $this->belongsTo(InterventionPlan::class);
    }

    public function beneficiary(): BelongsToThrough
    {
        return $this->belongsToThrough(Beneficiary::class, InterventionPlan::class);
    }

    public function organization(): BelongsToThrough
    {
        return $this->belongsToThrough(Organization::class, InterventionPlan::class);
    }
}
