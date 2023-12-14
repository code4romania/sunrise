<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToOrganization;
use App\Concerns\HasCitizenship;
use App\Concerns\HasEthnicity;
use App\Concerns\HasUlid;
use App\Enums\CaseStatus;
use App\Enums\CivilStatus;
use App\Enums\Gender;
use App\Enums\ResidenceEnvironment;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

        'status',

        'has_children',
        'doesnt_have_children',
        'children_total_count',
        'children_care_count',
        'children_under_10_care_count',
        'children_10_18_care_count',
        'children_18_care_count',
        'children_accompanying_count',
        'children',
        'children_notes',
    ];

    protected $casts = [
        'civil_status' => CivilStatus::class,
        'gender' => Gender::class,
        'birthdate' => 'date',
        'legal_residence_environment' => ResidenceEnvironment::class,
        'effective_residence_environment' => ResidenceEnvironment::class,
        'same_as_legal_residence' => 'boolean',
        'status' => CaseStatus::class,
        'has_children' => 'boolean',
        'children_total_count' => 'integer',
        'children_care_count' => 'integer',
        'children_under_10_care_count' => 'integer',
        'children_10_18_care_count' => 'integer',
        'children_18_care_count' => 'integer',
        'children_accompanying_count' => 'integer',
        'children' => 'collection',
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

    public function doesntHaveChildren(): Attribute
    {
        return Attribute::make(
            get: fn () => ! $this->has_children,
            set: fn ($value) => dd($value) && $this->has_children = ! $value,
        );
    }
}
