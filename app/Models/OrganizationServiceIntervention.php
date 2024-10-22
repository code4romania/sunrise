<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToOrganization;
use App\Enums\GeneralStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationServiceIntervention extends Model
{
    use HasFactory;
    use BelongsToOrganization;

    protected $fillable = [
        'service_intervention_id',
        'organization_service_id',
        'status',
    ];

    protected $casts = [
        'status' => GeneralStatus::class,
    ];

    public function serviceIntervention(): BelongsTo
    {
        return $this->belongsTo(ServiceIntervention::class);
    }

    public function organizationService(): BelongsTo
    {
        return $this->belongsTo(OrganizationService::class);
    }
}