<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;

class MonthlyPlan extends Model
{
    use HasFactory;
    use BelongsToThroughTrait;

    protected $fillable = [
        'intervention_plan_id',
        'start_date',
        'end_date',
        'case_manager_user_id',
        'specialists',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'specialists' => 'collection',
    ];

    public function organization(): BelongsToThrough
    {
        return $this->belongsToThrough(Organization::class, InterventionPlan::class);
    }

    public function interventionPlan(): BelongsTo
    {
        return $this->belongsTo(InterventionPlan::class);
    }

    public function beneficiary(): BelongsToThrough
    {
        return $this->belongsToThrough(Beneficiary::class, InterventionPlan::class);
    }

    public function monthlyPlanServices(): HasMany
    {
        return $this->hasMany(MonthlyPlanService::class);
    }

    public function monthlyPlanInterventions(): HasManyThrough
    {
        return $this->hasManyThrough(MonthlyPlanInterventions::class, MonthlyPlanService::class);
    }

    public function caseManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'case_manager_user_id');
    }

    public function getIntervalAttribute(): string
    {
        return \sprintf('%s - %s', $this->start_date->format('d.m.Y'), $this->end_date->format('d.m.Y'));
    }
}
