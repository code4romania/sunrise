<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Enums\Ternary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryAntecedents extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;

    protected $fillable = [
        'has_police_reports',
        'police_report_count',
        'has_medical_reports',
        'medical_report_count',
    ];

    protected $casts = [
        'has_medical_reports' => Ternary::class,
        'has_police_reports' => Ternary::class,
    ];
}
