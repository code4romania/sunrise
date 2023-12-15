<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;

enum AggressorLegalHistory: string
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
    use Concerns\Enums\HasLabel;

    case CRIMES = 'crimes';
    case CONTRAVENTIONS = 'contraventions';
    case PROTECTION_ORDER = 'protection_order';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.aggressor_legal_history';
    }
}
