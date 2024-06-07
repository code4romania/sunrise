<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Enums\Ternary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestedServices extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;

    protected $fillable = [
        'psychological_advice',
        'legal_advice',
        'legal_assistance',
        'family_counseling',
        'prenatal_advice',
        'social_advice',
        'medical_services',
        'medical_payment',
        'securing_residential_spaces',
        'occupational_program_services',
        'educational_services_for_children',
        'temporary_shelter_services',
        'protection_order',
        'crisis_assistance',
        'safety_plan',
        'other_services',
        'other_services_description',
    ];

    protected $casts = [
        'psychological_advice' => Ternary::class,
        'legal_advice' => Ternary::class,
        'legal_assistance' => Ternary::class,
        'family_counseling' => Ternary::class,
        'prenatal_advice' => Ternary::class,
        'social_advice' => Ternary::class,
        'medical_services' => Ternary::class,
        'medical_payment' => Ternary::class,
        'securing_residential_spaces' => Ternary::class,
        'occupational_program_services' => Ternary::class,
        'educational_services_for_children' => Ternary::class,
        'temporary_shelter_services' => Ternary::class,
        'protection_order' => Ternary::class,
        'crisis_assistance' => Ternary::class,
        'safety_plan' => Ternary::class,
        'other_services' => Ternary::class,
    ];
}
