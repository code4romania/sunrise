<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum ChildAggressorRelationship: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Enums\HasLabel;

    case CHILD = 'child';
    case OTHER_RELATIONSHIP = 'other_relationship';
    case FROM_ANOTHER_RELATIONSHIP = 'from_another_relationship';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.child_aggressor_relationships';
    }
}
