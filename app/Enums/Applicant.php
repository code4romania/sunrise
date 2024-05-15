<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel;

enum Applicant: string
{
    use Arrayable;
    use Comparable;
    use HasLabel;

    case BENFECIARY = 'beneficiary';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.applicant';
    }
}
