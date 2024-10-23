<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;

class InterventionService extends Model
{
    use HasFactory;
    use BelongsToThroughTrait;

    protected $fillable = [
        'intervention_plan_id',
        'organization_service_id',
        'user_id',
        'institution',
        'start_date',
        'end_date',
        'objections',
    ];

    public function interventionPlan(): BelongsTo
    {
        return $this->belongsTo(InterventionPlan::class);
    }

    public function organizationService(): BelongsTo
    {
        return $this->belongsTo(OrganizationService::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsToThrough
    {
        return $this->belongsToThrough(Organization::class, InterventionPlan::class);
    }

    public function beneficiaryInterventions(): HasMany
    {
        return $this->hasMany(BeneficiaryIntervention::class);
    }

    public function counselingSheet(): HasOne
    {
        return $this->hasOne(ServiceCounselingSheet::class);
    }
}
