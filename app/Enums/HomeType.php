<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum HomeType: string implements HasLabel
{
    use Enums\HasLabel;
    use Arrayable;
    use Comparable;

    case INDIVIDUAL_HOUSE = 'individual_house';
    case BUILDING_WITH_MULTIPLE_HOUSES = 'building_with_multiple_houses';
    case APARTMENT = 'apartment';
    case STUDIO = 'studio';
    case ROOM = 'room';
    case SPACE_IN_BUILDING_WITH_NON_RESIDENTIAL_DESTINATION = 'space_in_building_with_non_residential_destination';
    case OTHER = 'other';

    public function labelKeyPrefix(): ?string
    {
        return 'enum.home_type';
    }
}
