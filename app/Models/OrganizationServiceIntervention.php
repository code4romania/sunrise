<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToOrganization;
use App\Concerns\HasGeneralStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;

class OrganizationServiceIntervention extends Model
{
    use HasFactory;
    use BelongsToOrganization;
    use HasGeneralStatus;
    use BelongsToThroughTrait;

    protected $fillable = [
        'service_intervention_id',
        'organization_service_id',
    ];

    public function serviceIntervention(): BelongsTo
    {
        return $this->belongsTo(ServiceIntervention::class)
            ->active();
    }

    public function serviceInterventionWithoutStatusCondition(): BelongsTo
    {
        return $this->belongsTo(ServiceIntervention::class, 'service_intervention_id');
    }

    public function organizationService(): BelongsTo
    {
        return $this->belongsTo(OrganizationService::class);
    }

    public function beneficiaryInterventions(): HasMany
    {
        return $this->hasMany(BeneficiaryIntervention::class);
    }
}
