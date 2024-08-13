<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailedEvaluationResult extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;

    protected $fillable = [
        'psychological_advice',
        'legal_advice',
        'legal_assistance',
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
        'recommendations_for_intervention_plan',
    ];

    protected $casts = [
        'psychological_advice' => 'boolean',
        'legal_advice' => 'boolean',
        'legal_assistance' => 'boolean',
        'prenatal_advice' => 'boolean',
        'social_advice' => 'boolean',
        'medical_services' => 'boolean',
        'medical_payment' => 'boolean',
        'securing_residential_spaces' => 'boolean',
        'occupational_program_services' => 'boolean',
        'educational_services_for_children' => 'boolean',
        'temporary_shelter_services' => 'boolean',
        'protection_order' => 'boolean',
        'crisis_assistance' => 'boolean',
        'safety_plan' => 'boolean',
        'other_services' => 'boolean',
    ];
}
