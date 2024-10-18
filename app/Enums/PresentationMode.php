<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;
use Filament\Support\Contracts\HasLabel;

enum PresentationMode: string implements HasLabel
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
    use Concerns\Enums\HasLabel;

    case SPONTANEOUS = 'spontaneous';
    case SCHEDULED = 'scheduled';
    case FORWARDED = 'forwarded';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.presentation_mode';
    }
}
