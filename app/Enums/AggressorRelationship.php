<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;
use Filament\Support\Contracts\HasLabel;

enum AggressorRelationship: string implements HasLabel
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
    use Concerns\Enums\HasLabel;

    case MARITAL = 'marital';
    case CONSENSUAL = 'consensual';
    case FORMER_PARTNER = 'former_partner';
    case PARENTAL = 'parental';
    case FILIAL = 'filial';
    case OTHER_RELATED = 'other_related';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.aggressor_relationship';
    }
}
