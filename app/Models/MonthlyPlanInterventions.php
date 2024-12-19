<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyPlanInterventions extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_plan_service_id',
        'service_intervention_id',
        'objections',
        'observations',
    ];

    public function monthlyPlan(): BelongsTo
    {
        return $this->belongsTo(MonthlyPlan::class);
    }

    public function monthlyPlanService(): BelongsTo
    {
        return $this->belongsTo(MonthlyPlanService::class);
    }

    public function serviceIntervention(): BelongsTo
    {
        return $this->belongsTo(ServiceIntervention::class);
    }
}
