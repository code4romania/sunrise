<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum ActivityDescription: string implements HasLabel
{
    use Enums\HasLabel;
    use Arrayable;
    use Comparable;

    case CREATED = 'created';
    case RETRIEVED = 'retrieved';
    case UPDATED = 'updated';

    case DELETED = 'deleted';

    case LOGGED_IN = 'logged_in';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.activity_description';
    }
}
