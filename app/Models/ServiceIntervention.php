<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasGeneralStatus;
use App\Models\Scopes\SortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class ServiceIntervention extends Model
{
    use HasFactory;
    use HasGeneralStatus;

    protected $fillable = [
        'service_id',
        'name',
        'identifier',
        'sort',
    ];

    protected static function booted()
    {
        parent::booted();
        static::addGlobalScope(new SortOrder);
        static::creating(function (ServiceIntervention $model): void {
            if (! isset($model->sort)) {
                $model->sort = static::max('sort') + 1;
            }
            if (! isset($model->identifier)) {
                $model->identifier = Str::slug($model->name);
            }
        });
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function organizationIntervention(): HasOne
    {
        return $this->hasOne(OrganizationServiceIntervention::class)
            ->whereHas('organizationService');
    }

    public function allOrganizationIntervention(): HasMany
    {
        return $this->hasMany(OrganizationServiceIntervention::class);
    }

    public function getOrganizationsCountAttribute(): int
    {
        return $this->allOrganizationIntervention()->count();
    }

    public function getInstitutionsCountAttribute(): int
    {
        return $this->allOrganizationIntervention()
            ->with('organization')
            ->get()
            ->map(fn (OrganizationServiceIntervention $item) => $item->organization)
            ->unique('institution_id')
            ->count();
    }
}
