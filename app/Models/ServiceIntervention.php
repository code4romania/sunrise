<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasGeneralStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceIntervention extends Model
{
    use HasFactory;
    use HasGeneralStatus;

    protected $fillable = [
        'service_id',
        'name',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function organizationIntervention(): HasOne
    {
        return $this->hasOne(OrganizationServiceIntervention::class);
    }
}
