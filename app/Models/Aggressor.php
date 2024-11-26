<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\HasCitizenship;
use App\Concerns\LogsActivityOptions;
use App\Enums\AggressorLegalHistory;
use App\Enums\AggressorRelationship;
use App\Enums\CivilStatus;
use App\Enums\Drug;
use App\Enums\Gender;
use App\Enums\Occupation;
use App\Enums\ProtectionOrder;
use App\Enums\Studies;
use App\Enums\Ternary;
use App\Enums\Violence;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Aggressor extends Model
{
    use HasCitizenship;
    use HasFactory;
    use BelongsToBeneficiary;
    use LogsActivity;
    use LogsActivityOptions;

    protected $fillable = [
        'age',
        'civil_status',
        'drugs',
        'gender',
        'has_drug_history',
        'has_psychiatric_history',
        'has_violence_history',
        'legal_history',
        'occupation',
        'psychiatric_history_notes',
        'relationship',
        'studies',
        'violence_types',
        'has_protection_order',
        'electronically_monitored',
        'protection_order_notes',
    ];

    protected $casts = [
        'age' => 'integer',
        'civil_status' => CivilStatus::class,
        'drugs' => AsEnumCollection::class . ':' . Drug::class,
        'gender' => Gender::class,
        'has_drug_history' => Ternary::class,
        'has_psychiatric_history' => Ternary::class,
        'has_violence_history' => Ternary::class,
        'legal_history' => AsEnumCollection::class . ':' . AggressorLegalHistory::class,
        'occupation' => Occupation::class,
        'relationship' => AggressorRelationship::class,
        'studies' => Studies::class,
        'violence_types' => AsEnumCollection::class . ':' . Violence::class,
        'has_protection_order' => ProtectionOrder::class,
        'electronically_monitored' => Ternary::class,
    ];
}
