<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Beneficiary;
use App\Models\BeneficiaryPartner;

trait HasEffectiveAddress
{
    protected static function bootHasEffectiveAddress(): void
    {
        static::creating(fn (Beneficiary | BeneficiaryPartner $model) => self::copyLegalResidenceToEffectiveResidence($model));

        static::updating(fn (Beneficiary | BeneficiaryPartner $model) => self::copyLegalResidenceToEffectiveResidence($model));
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
