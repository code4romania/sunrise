<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum ProtectionOrder: string implements HasLabel
{
    use Enums\HasLabel;
    use Arrayable;
    use Comparable;

    case TEMPORARY = 'temporary';
    case ISSUED_BY_COURT = 'issued_by_court';
    case NO = 'no';
    case UNKNOWN = 'unknown';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.protection_order';
    }
}
