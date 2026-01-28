<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns;
use Filament\Support\Contracts\HasLabel;

enum IDType: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Concerns\Enums\HasLabel;

    case BIRTH_CERTIFICATE = 'birth_certificate';
    case ID_CARD = 'id_card';
    case NATIONAL_PASSPORT = 'national_passport';
    case FOREIGN_PASSPORT = 'foreign_passport';
    case OTHER = 'other';
    case NONE = 'none';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.id_type';
    }
}
