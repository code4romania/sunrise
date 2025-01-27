<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum AddressType: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Comparable;
    use Enums\Arrayable;

    case EFFECTIVE_RESIDENCE = 'effective_residence';
    case LEGAL_RESIDENCE = 'legal_residence';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.address_type';
    }
}
