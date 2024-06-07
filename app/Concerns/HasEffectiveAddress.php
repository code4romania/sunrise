<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Enums\ResidenceEnvironment;
use App\Models\Beneficiary;
use App\Models\BeneficiaryPartner;
use App\Models\City;
use App\Models\County;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasEffectiveAddress
{
    public function initializeHasEffectiveAddress(): void
    {
        $this->fillable[] = 'legal_residence_county_id';
        $this->fillable[] = 'legal_residence_city_id';
        $this->fillable[] = 'legal_residence_address';
        $this->fillable[] = 'legal_residence_environment';

        $this->fillable[] = 'same_as_legal_residence';

        $this->fillable[] = 'effective_residence_county_id';
        $this->fillable[] = 'effective_residence_city_id';
        $this->fillable[] = 'effective_residence_address';
        $this->fillable[] = 'effective_residence_environment';

        $this->casts['effective_residence_environment'] = ResidenceEnvironment::class;
        $this->casts['legal_residence_environment'] = ResidenceEnvironment::class;
    }

    protected static function bootHasEffectiveAddress(): void
    {
        static::creating(fn (Beneficiary | BeneficiaryPartner $model) => self::copyLegalResidenceToEffectiveResidence($model));

        static::updating(fn (Beneficiary | BeneficiaryPartner $model) => self::copyLegalResidenceToEffectiveResidence($model));
    }

    public function legalResidenceCounty(): BelongsTo
    {
        return $this->belongsTo(County::class, 'legal_residence_county_id');
    }

    public function legalResidenceCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'legal_residence_city_id');
    }

    public function effectiveResidenceCounty(): BelongsTo
    {
        return $this->belongsTo(County::class, 'effective_residence_county_id');
    }

    public function effectiveResidenceCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'effective_residence_city_id');
    }

    protected static function copyLegalResidenceToEffectiveResidence(Beneficiary | BeneficiaryPartner $model): void
    {
        if ($model->same_as_legal_residence) {
            $model->effective_residence_county_id = $model->legal_residence_county_id;
            $model->effective_residence_city_id = $model->legal_residence_city_id;
            $model->effective_residence_address = $model->legal_residence_address;
            if (isset($model->legal_residence_environment)) {
                $model->effective_residence_environment = $model->legal_residence_environment;
            }
        }
    }
}
