<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel;

enum RiskFactorsSchema: string
{
    use Arrayable;
    use Comparable;
    use HasLabel;

    case AGGRESSOR_PRESENT_RISK_RELATED_TO_VICES = 'aggressor_present_risk_related_to_vices';
    case AGGRESSOR_IS_POSSESSIVE_OR_JEALOUS = 'aggressor_is_possessive_or_jealous';
    case AGGRESSOR_HAVE_MENTAL_PROBLEMS = 'aggressor_have_mental_problems';
    case AGGRESSOR_PRESENT_MANIFESTATIONS_OF_ECONOMIC_STRESS = 'aggressor_present_manifestations_of_economic_stress';

    protected function labelKeyPrefix(): ?string
    {
        return 'beneficiary.section.initial_evaluation.labels';
    }
}
