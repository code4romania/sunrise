<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns;
use Filament\Support\Contracts\HasLabel;

enum ActLocation: string implements HasLabel
{
    use Arrayable;
    use Comparable;
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
