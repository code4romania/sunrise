<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class MonthlyPlan extends Model
{
    use HasFactory;

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
        'specialists' => Collection::class,
    ];

    public function interventionPlan(): BelongsTo
    {
        return $this->belongsTo(InterventionPlan::class);
    }

    public function monthlyPlanServices(): HasMany
    {
        return $this->hasMany(MonthlyPlanService::class);
    }

    public function monthlyPlanInterventions(): HasMany
    {
        return $this->hasMany(MonthlyPlanInterventions::class);
    }
}
