<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasGeneralStatus;
use App\Concerns\HasSortOrder;
use App\Enums\CounselingSheet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;
    use HasGeneralStatus;
    use HasSortOrder;

    protected $fillable = [
        'name',
        'counseling_sheet',
    ];

    protected $casts = [
        'counseling_sheet' => CounselingSheet::class,
    ];

    public function serviceInterventions(): HasMany
    {
        return $this->hasMany(ServiceIntervention::class);
    }

    public function activeServiceInterventions(): HasMany
    {
        return $this->serviceInterventions()->active();
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(Intervention::class);
    }

    public function organizationServices(): HasMany
    {
        return $this->hasMany(OrganizationService::class);
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, OrganizationService::class);
    }

    public function getInstitutionsCountAttribute()
    {
        return $this->organizations()->distinct('institution_id')->count('institution_id');
    }
}
