<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\HasEffectiveAddress;
use App\Enums\Occupation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryPartner extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;
    use HasEffectiveAddress;

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

    protected $casts = [
        'occupation' => Occupation::class,
    ];
}
