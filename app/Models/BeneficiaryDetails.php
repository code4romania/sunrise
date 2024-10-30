<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Enums\HomeOwnership;
use App\Enums\Income;
use App\Enums\Occupation;
use App\Enums\Studies;
use App\Enums\Ternary;
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
    ];
}
