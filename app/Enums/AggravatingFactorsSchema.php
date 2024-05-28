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

    case FR_S5Q1 = 'FR_S5Q1';
    case FR_S5Q2 = 'FR_S5Q2';
    case FR_S5Q3 = 'FR_S5Q3';
    case FR_S5Q4 = 'FR_S5Q4';
    case FR_S5Q5 = 'FR_S5Q5';

    protected function labelKeyPrefix(): ?string
    {
        return 'beneficiary.section.initial_evaluation.labels';
    }
}
