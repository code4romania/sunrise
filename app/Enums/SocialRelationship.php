<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum SocialRelationship: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case FRIEND = 'friend';
    case COWORKER = 'coworker';
    case SUPPORT_GROUP = 'support_group';
    case OTHER = 'other';

    public function labelKeyPrefix(): ?string
    {
        return 'enum.social_relationship';
    }
}
