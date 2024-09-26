<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GeneralStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceIntervention extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'status',
    ];

    protected $casts = [
        'status' => GeneralStatus::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function organizationIntervention(): HasOne
    {
        return $this->hasOne(OrganizationServiceIntervention::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', GeneralStatus::ACTIVE);
    }
}
