<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToOrganization;
use App\Concerns\HasCaseStatus;
use App\Concerns\HasCitizenship;
use App\Concerns\HasEthnicity;
use App\Concerns\HasUlid;
use App\Enums\CaseStatus;
use App\Enums\CivilStatus;
use App\Enums\Gender;
use App\Enums\HomeOwnership;
use App\Enums\Income;
use App\Enums\Occupation;
use App\Enums\ResidenceEnvironment;
use App\Enums\Studies;
use App\Enums\Ternary;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Beneficiary extends Model
{
    use BelongsToOrganization;
    use HasCaseStatus;
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

        'status',

        'doesnt_have_children',
        'children_total_count',
        'children_care_count',
        'children_under_10_care_count',
        'children_10_18_care_count',
        'children_18_care_count',
        'children_accompanying_count',
        'children',
        'children_notes',

        'has_family_doctor',
        'family_doctor_name',
        'family_doctor_contact',
        'psychiatric_history',
        'psychiatric_history_notes',
        'criminal_history',
        'criminal_history_notes',
        'studies',
        'occupation',
        'workplace',
        'income',
        'elder_care_count',
        'homeownership',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'children_10_18_care_count' => 'integer',
        'children_18_care_count' => 'integer',
        'children_accompanying_count' => 'integer',
        'children_care_count' => 'integer',
        'children_total_count' => 'integer',
        'children_under_10_care_count' => 'integer',
        'children' => 'collection',
        'civil_status' => CivilStatus::class,
        'criminal_history' => Ternary::class,
        'effective_residence_environment' => ResidenceEnvironment::class,
        'elder_care_count' => 'integer',
        'gender' => Gender::class,
        'doesnt_have_children' => 'boolean',
        'has_family_doctor' => Ternary::class,
        'homeownership' => HomeOwnership::class,
        'income' => Income::class,
        'legal_residence_environment' => ResidenceEnvironment::class,
        'occupation' => Occupation::class,
        'psychiatric_history' => Ternary::class,
        'same_as_legal_residence' => 'boolean',
        'status' => CaseStatus::class,
        'studies' => Studies::class,
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
