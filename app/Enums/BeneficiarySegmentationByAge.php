<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum BeneficiarySegmentationByAge: string implements HasLabel
{
    use Enums\HasLabel;
    use Arrayable;
    use Comparable;

    case MINOR = 'minor';
    case MAJOR = 'major';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.beneficiary_segmentation_by_age';
    }
}
