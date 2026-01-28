<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum Gender: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Concerns\Enums\HasLabel;

    case FEMALE = 'female';
    case MALE = 'male';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.gender';
    }
}
