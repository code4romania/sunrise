<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskFactors extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;

    protected $fillable = [
        'previous_acts_of_violence',
        'previous_acts_of_violence_description',
        'violence_against_children_or_family_members',
        'violence_against_children_or_family_members_description',
        'abuser_exhibited_generalized_violent',
        'abuser_exhibited_generalized_violent_description',
        'protection_order_in_past',
        'protection_order_in_past_description',
        'abuser_violated_protection_order',
        'abuser_violated_protection_order_description',
        'frequency_of_violence_acts',
        'frequency_of_violence_acts_description',
        'use_weapons_in_act_of_violence',
        'use_weapons_in_act_of_violence_description',
        'controlling_and_isolating',
        'controlling_and_isolating_description',
        'stalked_or_harassed',
        'stalked_or_harassed_description',
        'sexual_violence',
        'sexual_violence_description',
        'death_threats',
        'death_threats_description',
        'strangulation_attempt',
        'strangulation_attempt_description',
        'FR_S3Q1',
        'FR_S3Q1_description',
        'FR_S3Q2',
        'FR_S3Q2_description',
        'FR_S3Q3',
        'FR_S3Q3_description',
        'FR_S3Q4',
        'FR_S3Q4_description',
        'FR_S4Q1',
        'FR_S4Q1_description',
        'FR_S4Q2',
        'FR_S4Q2_description',
        'FR_S5Q1',
        'FR_S5Q1_description',
        'FR_S5Q2',
        'FR_S5Q2_description',
        'FR_S5Q3',
        'FR_S5Q3_description',
        'FR_S5Q4',
        'FR_S5Q4_description',
        'FR_S5Q5',
        'FR_S5Q5_description',
        'FR_S6Q1',
        'FR_S6Q1_description',
        'FR_S6Q2',
        'FR_S6Q2_description',
    ];
}
