<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BenefitService extends Model
{
    use HasFactory;

    protected $fillable = [
        'intervention_plan_id',
        'benefit_id',
        'benefit_types',
        'description',
    ];

    protected $casts = [
        'benefit_types' => 'json',
    ];

    public function interventionPlan(): BelongsTo
    {
        return $this->belongsTo(InterventionPlan::class);
    }

    public function benefit(): BelongsTo
    {
        return $this->belongsTo(Benefit::class);
    }
}
