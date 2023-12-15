<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;

enum Notifier: string
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
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
