<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasCitizenship;
use App\Enums\AggressorLegalHistory;
use App\Enums\AggressorRelationship;
use App\Enums\CivilStatus;
use App\Enums\Drug;
use App\Enums\Gender;
use App\Enums\Occupation;
use App\Enums\Studies;
use App\Enums\Ternary;
use App\Enums\Violence;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aggressor extends Model
{
    use HasCitizenship;
    use HasFactory;

    protected $fillable = [
        'age',
        'civil_status',
        'drugs',
        'has_drug_history',
        'has_protection_order',
        'has_psychiatric_history',
        'has_violence_history',
        'legal_history',
        'occupation',
        'protection_order_notes',
        'psychiatric_history_notes',
        'relationship',
        'studies',
        'violence_types',
    ];

    protected $casts = [
        'age' => 'integer',
        'civil_status' => CivilStatus::class,
        'drugs' => AsEnumCollection::class . ':' . Drug::class,
        'gender' => Gender::class,
        'has_drug_history' => Ternary::class,
        'has_protection_order' => Ternary::class,
        'has_psychiatric_history' => Ternary::class,
        'has_violence_history' => Ternary::class,
        'legal_history' => AsEnumCollection::class . ':' . AggressorLegalHistory::class,
        'occupation' => Occupation::class,
        'relationship' => AggressorRelationship::class,
        'studies' => Studies::class,
        'violence_types' => AsEnumCollection::class . ':' . Violence::class,
    ];

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
