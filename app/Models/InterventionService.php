<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasIntervalAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;

class InterventionService extends Model
{
    use HasFactory;
    use BelongsToThroughTrait;
    use HasIntervalAttribute;

    protected $fillable = [
        'intervention_plan_id',
        'organization_service_id',
        'specialist_id',
        'institution',
        'start_date',
        'objections',
    ];

    public function interventionPlan(): BelongsTo
    {
        return $this->belongsTo(InterventionPlan::class);
    }

    public function organizationService(): BelongsTo
    {
        return $this->belongsTo(OrganizationService::class)
            ->active();
    }

    public function organizationServiceWithoutStatusCondition(): BelongsTo
    {
        return $this->belongsTo(OrganizationService::class, 'organization_service_id');
    }

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(Specialist::class);
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

    public function meetings(): HasManyThrough
    {
        return $this->hasManyThrough(InterventionMeeting::class, BeneficiaryIntervention::class);
    }

    public function beneficiary(): BelongsToThrough
    {
        return $this->belongsToThrough(Beneficiary::class, InterventionPlan::class);
    }
}
