<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasGeneralStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Result extends Model
{
    use HasFactory;
    use HasGeneralStatus;

    protected $fillable = [
        'name',
    ];

    public function interventionPlanResults(): HasMany
    {
        return $this->hasMany(InterventionPlanResult::class);
    }

    public function organizations(): Builder
    {
        return Organization::whereHas('beneficiaries', function ($query) {
            $query->whereHas('interventionPlan.results', function ($query) {
                $query->where('result_id', $this->id);
            });
        });
    }

    public function getOrganizationsCountAttribute(): int
    {
        return $this->organizations()->count();
    }

    public function getInstitutionsCountAttribute(): int
    {
        return $this->organizations()->distinct('institution_id')->count('institution_id');
    }
}
