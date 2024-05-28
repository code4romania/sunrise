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

    case FR_S3Q1 = 'FR_S3Q1';
    case FR_S3Q2 = 'FR_S3Q2';
    case  FR_S3Q3 = 'FR_S3Q3';
    case FR_S3Q4 = 'FR_S3Q4';

    protected function labelKeyPrefix(): ?string
    {
        return 'beneficiary.section.initial_evaluation.labels';
    }
}
