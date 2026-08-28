<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\LogsActivityOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Znck\Eloquent\Relations\BelongsToThrough;

class MonthlyPlanInterventions extends Model
{
    use HasFactory;
    use LogsActivityOptions;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $fillable = [
        'monthly_plan_service_id',
        'service_intervention_id',
        'objections',
        'observations',
        'expected_results',
        'procedure',
        'indicators',
        'achievement_degree',
    ];

    public function monthlyPlanService(): BelongsTo
    {
        return $this->belongsTo(MonthlyPlanService::class);
    }

    public function serviceIntervention(): BelongsTo
    {
        return $this->belongsTo(ServiceIntervention::class);
    }

    public function beneficiary(): BelongsToThrough
    {
        return $this->belongsToThrough(Beneficiary::class, [InterventionPlan::class, MonthlyPlan::class, MonthlyPlanService::class]);
    }
}
