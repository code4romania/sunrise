<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum CounselingSheet: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case PSYCHOLOGICAL_ASSISTANCE = 'psychological_assistance';
    case LEGAL_ASSISTANCE = 'legal_assistance';
    case SOCIAL_ASSISTANCE = 'social_assistance';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.counseling_sheet';
    }
}
