<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToOrganization;
use App\Concerns\HasGeneralStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationService extends Model
{
    use BelongsToOrganization;
    use HasFactory;
    use HasGeneralStatus;

    protected $fillable = [
        'service_id',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class)
            ->active();
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function serviceWithoutStatusCondition(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(OrganizationServiceIntervention::class);
    }

    public function interventionServices(): HasMany
    {
        return $this->hasMany(InterventionService::class);
    }
}
