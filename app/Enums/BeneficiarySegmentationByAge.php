<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum BeneficiarySegmentationByAge: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case MINOR = 'minor';
    case MAJOR = 'major';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.beneficiary_segmentation_by_age';
    }
}
