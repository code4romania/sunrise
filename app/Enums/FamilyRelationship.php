<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum FamilyRelationship: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case PARTNER = 'partner';
    case MOTHER = 'mother';
    case FATHER = 'father';
    case SISTER = 'sister';
    case BROTHER = 'brother';
    case OTHER = 'other';

    public function labelKeyPrefix(): ?string
    {
        return 'enum.family_relationship';
    }
}
