<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum Applicant: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Enums\HasLabel;

    case BENEFICIARY = 'beneficiary';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.applicant';
    }
}
