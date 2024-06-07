<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel;

enum ViolencesTypesSchema: string
{
    use Arrayable;
    use Comparable;
    use HasLabel;

    case FREQUENCY_OF_VIOLENCE_ACTS = 'frequency_of_violence_acts';
    case USE_WEAPONS_IN_ACT_OF_VIOLENCE = 'use_weapons_in_act_of_violence';
    case CONTROLLING_AND_ISOLATING = 'controlling_and_isolating';
    case STALKED_OR_HARASSED = 'stalked_or_harassed';
    case SEXUAL_VIOLENCE = 'sexual_violence';
    case DEATH_THREATS = 'death_threats';
    case STRANGULATION_ATTEMPT = 'strangulation_attempt';

    protected function labelKeyPrefix(): ?string
    {
        return 'beneficiary.section.initial_evaluation.labels';
    }
}
