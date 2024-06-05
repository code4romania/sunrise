<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel;

enum AggravatingFactorsSchema: string
{
    use Arrayable;
    use Comparable;
    use HasLabel;

    case SEPARATION = 'separation';
    case AGGRESSOR_PARENT_HAS_CONTACT_WITH_CHILDREN = 'aggressor_parent_has_contact_with_children';
    case AGGRESSOR_PARENT_THREATEN_THE_VICTIM_IN_THE_VISITATION_PROGRAM = 'aggressor_parent_threaten_the_victim_in_the_visitation_program';
    case CHILDREN_FROM_OTHER_MARRIAGE_ARE_INTEGRATED_INTO_FAMILY = 'children_from_other_marriage_are_integrated_into_family';
    case DOMESTIC_VIOLENCE_DURING_PREGNANCY = 'domestic_violence_during_pregnancy';

    protected function labelKeyPrefix(): ?string
    {
        return 'beneficiary.section.initial_evaluation.labels';
    }
}
