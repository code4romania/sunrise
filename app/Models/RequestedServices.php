<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
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
}
