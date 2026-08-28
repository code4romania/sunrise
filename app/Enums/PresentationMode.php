<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum PresentationMode: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Concerns\Enums\HasLabel;

    case SPONTANEOUS = 'spontaneous';
    case SCHEDULED = 'scheduled';
    case FORWARDED = 'forwarded';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.presentation_mode';
    }
}
