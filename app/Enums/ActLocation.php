<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;
use Filament\Support\Contracts\HasLabel;

enum ActLocation: string implements HasLabel
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
    use Concerns\Enums\HasLabel;

    case DOMICILE = 'domicile';
    case RESIDENCE = 'residence';
    case PUBLIC = 'public';
    case WORK = 'work';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.act_location';
    }
}
