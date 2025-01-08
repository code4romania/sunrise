<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum AllowancePerson: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case BENEFICIARY = 'beneficiary';
    case OTHER = 'other';
    case UNKNOWN = 'unknown';

    public function labelKeyPrefix(): ?string
    {
        return 'enum.allowance_person';
    }
}
