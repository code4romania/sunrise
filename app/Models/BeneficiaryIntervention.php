<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;

class BeneficiaryIntervention extends Model
{
    use HasFactory;
    use BelongsToThroughTrait;

    protected $fillable = [
        'organization_service_intervention_id',
        'intervention_service_id',
        'user_id',
        'start_date',
        'end_date',
        'objections',
        'expected_results',
        'procedure',
        'indicators',
        'achievement_degree',
    ];

    public function interventionService(): BelongsTo
    {
        return $this->belongsTo(InterventionService::class);
    }

    public function organizationServiceIntervention(): BelongsTo
    {
        return $this->belongsTo(OrganizationServiceIntervention::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsToThrough
    {
        return $this->belongsToThrough(Organization::class, OrganizationServiceIntervention::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(InterventionMeeting::class);
    }
}
