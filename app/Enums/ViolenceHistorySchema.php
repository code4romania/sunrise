<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel;

enum ViolenceHistorySchema: string
{
    use Arrayable;
    use Comparable;
    use HasLabel;

    case PREVIOUS_ACTS_OF_VIOLENCE = 'previous_acts_of_violence';
    case VIOLENCE_AGAINST_CHILDREN_OR_FAMILY_MEMBERS = 'violence_against_children_or_family_members';
    case ABUSER_EXHIBITED_GENERALIZED_VIOLENT = 'abuser_exhibited_generalized_violent';
    case PROTECTION_ORDER_IN_PAST = 'protection_order_in_past';
    case ABUSER_VIOLATED_PROTECTION_ORDER = 'abuser_violated_protection_order';

    protected function labelKeyPrefix(): ?string
    {
        return 'beneficiary.section.initial_evaluation.labels';
    }
}
