<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum AreaType: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case INTERNATIONAL = 'international';
    case NATIONAL = 'national';

    case REGIONAL = 'regional';

    case COUNTY = 'county';
    case LOCAL = 'local';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.area_type';
    }
}
