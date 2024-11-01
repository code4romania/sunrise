<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\Occupation;

trait HasVerticalHeaderOccupation
{
    public function getVerticalHeader(): array
    {
        return Occupation::options();
    }

    public function getVerticalHeaderKey(): string
    {
        return 'occupation';
    }
}
