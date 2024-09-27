<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToOrganization;
use App\Enums\GeneralStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationService extends Model
{
    use HasFactory;
    use BelongsToOrganization;

    protected $fillable = [
        'service_id',
        'status',
    ];

    protected $casts = [
        'status' => GeneralStatus::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(OrganizationServiceIntervention::class);
    }
}
