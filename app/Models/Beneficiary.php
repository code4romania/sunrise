<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Gender;
use App\Concerns\HasUlid;
use App\Enums\CivilStatus;
use App\Concerns\HasEthnicity;
use App\Concerns\HasCitizenship;
use App\Enums\ResidenceEnvironment;
use App\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Beneficiary extends Model
{
    use BelongsToOrganization;
    use HasCitizenship;
    use HasEthnicity;
    use HasFactory;
    use HasUlid;

    protected $fillable = [
        'first_name',
        'last_name',
        'prior_name',

        'civil_status',
        'cnp',
        'gender',

        'birthdate',
        'birthplace',
        'ethnicity',

        'id_type',
        'id_serial',
        'id_number',

        'legal_residence_county_id',
        'legal_residence_city_id',
        'legal_residence_address',
        'legal_residence_environment',

        'same_as_legal_residence',
        'effective_residence_county_id',
        'effective_residence_city_id',
        'effective_residence_address',
        'effective_residence_environment',

        'primary_phone',
        'backup_phone',
        'contact_notes',
    ];

    protected $casts = [
        'civil_status' => CivilStatus::class,
        'gender' => Gender::class,
        'birthdate' => 'date',
        'legal_residence_environment' => ResidenceEnvironment::class,
        'effective_residence_environment' => ResidenceEnvironment::class,
        'same_as_legal_residence' => 'boolean',
    ];

    public function legalResidenceCounty(): BelongsTo
    {
        return $this->belongsTo(County::class, 'legal_residence_county_id');
    }

    public function legalResidenceCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'legal_residence_city_id');
    }

    public function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->birthdate?->age,
        );
    }
}
