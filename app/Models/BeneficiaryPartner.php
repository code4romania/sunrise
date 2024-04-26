<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryPartner extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;

    protected $fillable = [
        'last_name',
        'first_name',
        'age',
        'occupation',
        'legal_residence_county_id',
        'legal_residence_city_id',
        'legal_residence_address',
        'same_as_legal_residence',
        'effective_residence_county_id',
        'effective_residence_city_id',
        'effective_residence_address',
        'observations',
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(fn (BeneficiaryPartner $model) => self::copyLegalResidenceToEffectiveResidence($model));

        self::updating(fn (BeneficiaryPartner $model) => self::copyLegalResidenceToEffectiveResidence($model));
    }

    // TODO after merge this pr and #13 make a trait with this function
    private static function copyLegalResidenceToEffectiveResidence(self $model): void
    {
        if ($model->same_as_legal_residence) {
            $model->effective_residence_county_id = $model->legal_residence_county_id;
            $model->effective_residence_city_id = $model->legal_residence_city_id;
            $model->effective_residence_address = $model->legal_residence_address;
        }
    }
}
