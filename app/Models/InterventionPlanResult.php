<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterventionPlanResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'intervention_plan_id',
        'result_id',
        'user_id',
        'started_at',
        'ended_at',
        'retried_at',
        'retried',
        'lost_from_monitoring',
        'observations',
    ];

    public function interventionPlan(): BelongsTo
    {
        return $this->belongsTo(InterventionPlan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function result(): BelongsTo
    {
        return $this->belongsTo(Result::class);
    }

    public function activeResult(): BelongsTo
    {
        return $this->result()
            ->active();
    }
}
