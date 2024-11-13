<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Enums\DisabilityDegree;
use App\Enums\DisabilityType;
use App\Enums\Diseases;
use App\Enums\HomeOwnership;
use App\Enums\Income;
use App\Enums\IncomeSource;
use App\Enums\Occupation;
use App\Enums\Studies;
use App\Enums\Ternary;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryDetails extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;

    protected $fillable = [
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
        'health_insurance',
        'health_status',
        'observations_chronic_diseases',
        'observations_degenerative_diseases',
        'observations_mental_illness',
        'disabilities',
        'type_of_disability',
        'degree_of_disability',
        'observations_disability',
        'income_source',
    ];

    protected $casts = [
        'has_family_doctor' => Ternary::class,
        'psychiatric_history' => Ternary::class,
        'criminal_history' => Ternary::class,
        'studies' => Studies::class,
        'occupation' => Occupation::class,
        'income' => Income::class,
        'elder_care_count' => 'integer',
        'homeownership' => HomeOwnership::class,
        'health_insurance' => Ternary::class,
        'health_status' => AsEnumCollection::class . ':' . Diseases::class,
        'disabilities' => Ternary::class,
        'type_of_disability' => AsEnumCollection::class . ':' . DisabilityType::class,
        'degree_of_disability' => DisabilityDegree::class,
        'income_source' => AsEnumCollection::class . ':' . IncomeSource::class,
    ];
}
