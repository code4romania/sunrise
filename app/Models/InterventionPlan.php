<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class InterventionPlan extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;
    use BelongsToOrganization;

    protected $fillable = [
        'admit_date_in_center',
        'plan_date',
        'last_revise_date',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(InterventionService::class);
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(BenefitService::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(InterventionPlanResult::class);
    }

    public function beneficiaryInterventions(): HasManyThrough
    {
        return $this->hasManyThrough(BeneficiaryIntervention::class, InterventionService::class);
    }
}
