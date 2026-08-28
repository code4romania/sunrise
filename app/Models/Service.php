<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasGeneralStatus;
use App\Enums\CounselingSheet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;
    use HasGeneralStatus;

    protected $fillable = [
        'name',
        'identifier',
        'counseling_sheet',
        'sort',
    ];

    protected $casts = [
        'counseling_sheet' => CounselingSheet::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Service $model): void {
            if (! isset($model->sort)) {
                $model->sort = static::max('sort') + 1;
            }
            if (! isset($model->identifier)) {
                $model->identifier = Str::slug($model->name);
            }
        });
    }

    public function serviceInterventions(): HasMany
    {
        return $this->hasMany(ServiceIntervention::class);
    }

    public function activeServiceInterventions(): HasMany
    {
        return $this->serviceInterventions()->active();
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
