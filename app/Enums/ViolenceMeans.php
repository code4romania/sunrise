<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum ViolenceMeans: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Enums\HasLabel;

    case BODY_PARTS = 'body_parts';
    case CUTTING_OBJECTS = 'cutting_objects';
    case PIERCING_OBJECTS = 'piercing_objects';
    case BLUNT_OBJECTS = 'blunt_objects';
    case FIREARMS = 'firearms';
    case NO_ANSWER = 'no_answer';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.violence_means';
    }
}
