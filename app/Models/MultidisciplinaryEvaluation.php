<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\LogsActivityOptions;
use App\Enums\Applicant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class MultidisciplinaryEvaluation extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;
    use LogsActivity;
    use LogsActivityOptions;

    protected $fillable = [
        'applicant',
        'reporting_by',
        'medical_need',
        'professional_need',
        'emotional_and_psychological_need',
        'social_economic_need',
        'legal_needs',
        'extended_family',
        'family_social_integration',
        'income',
        'community_resources',
        'house',
        'risk',
    ];

    protected $casts = [
        'applicant' => Applicant::class,
    ];
}
