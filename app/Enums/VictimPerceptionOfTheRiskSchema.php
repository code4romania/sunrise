<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel;

enum VictimPerceptionOfTheRiskSchema: string
{
    use Arrayable;
    use Comparable;
    use HasLabel;

    case VICTIM_AFRAID_FOR_HIMSELF = 'victim_afraid_for_himself';
    case VICTIM_HAS_AN_ATTITUDE_OF_ACCEPTANCE = 'victim_has_an_attitude_of_acceptance';

    protected function labelKeyPrefix(): ?string
    {
        return 'beneficiary.section.initial_evaluation.labels';
    }
}
