<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns;
use Filament\Support\Contracts\HasLabel;

enum Notifier: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Concerns\Enums\HasLabel;

    case VICTIM = 'victim';
    case AGGRESSOR = 'aggressor';
    case CHILD = 'child';
    case OTHER_RELATED = 'other_related';
    case NEIGHBOUR = 'neighbour';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.notifier';
    }
}
