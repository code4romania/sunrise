<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum Applicant: string implements HasLabel
{
    use Enums\Arrayable;
    use Enums\Comparable;
    use Enums\HasLabel;

    case BENEFICIARY = 'beneficiary';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.applicant';
    }
}
