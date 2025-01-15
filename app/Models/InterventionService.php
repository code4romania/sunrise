<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToInterventionPlan;
use App\Concerns\HasIntervalAttribute;
use App\Concerns\LogsActivityOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InterventionService extends Model
{
    use HasFactory;
    use HasIntervalAttribute;
    use LogsActivityOptions;
    use BelongsToInterventionPlan;

    protected $fillable = [
        'intervention_plan_id',
        'organization_service_id',
        'specialist_id',
        'institution',
        'objections',
    ];

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
}
