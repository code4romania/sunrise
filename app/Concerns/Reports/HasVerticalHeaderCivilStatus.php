<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\CivilStatus;

trait HasVerticalHeaderCivilStatus
{
    public function getVerticalHeader(): array
    {
        return CivilStatus::options();
    }

    public function getVerticalHeaderKey(): string
    {
        return 'civil_status';
    }
}
