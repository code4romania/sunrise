<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum AggressorRelationship: string implements HasLabel
{
    use Arrayable;
    use Comparable;
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
