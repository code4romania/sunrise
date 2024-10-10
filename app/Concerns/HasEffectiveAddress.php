<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Enums\AddressType;
use App\Models\Address;
use App\Models\Beneficiary;
use App\Models\BeneficiaryPartner;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasEffectiveAddress
{
    public function initializeHasEffectiveAddress(): void
    {
        $this->fillable[] = 'same_as_legal_residence';
    }

    protected static function bootHasEffectiveAddress(): void
    {
        static::creating(fn (Beneficiary | BeneficiaryPartner $model) => self::copyLegalResidenceToEffectiveResidence($model));

        static::updating(fn (Beneficiary | BeneficiaryPartner $model) => self::copyLegalResidenceToEffectiveResidence($model));
    }

    public static function copyLegalResidenceToEffectiveResidence(Beneficiary | BeneficiaryPartner $model): void
    {
        if ($model->same_as_legal_residence && $model->legal_residence) {
            $address = $model->effective_residence ?? new Address();

            $address->country_id = $model->legal_residence->country_id;
            $address->county_id = $model->legal_residence->county_id;
            $address->city_id = $model->legal_residence->city_id;
            $address->address = $model->legal_residence->address;
            $address->addressable_id = $model->legal_residence->addressable_id;
            $address->addressable_type = $model->legal_residence->addressable_type;
            $address->environment = $model->legal_residence->environment;
            $address->address_type = AddressType::EFFECTIVE_RESIDENCE;
            $address->save();
        }
    }

    public function effective_residence(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable')
            ->where('address_type', AddressType::EFFECTIVE_RESIDENCE);
    }

    public function legal_residence(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable')
            ->where('address_type', AddressType::LEGAL_RESIDENCE);
    }
}
