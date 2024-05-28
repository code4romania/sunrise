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

    case FR_S4Q1 = 'FR_S4Q1';
    case FR_S4Q2 = 'FR_S4Q2';

    protected function labelKeyPrefix(): ?string
    {
        return 'beneficiary.section.initial_evaluation.labels';
    }
}
