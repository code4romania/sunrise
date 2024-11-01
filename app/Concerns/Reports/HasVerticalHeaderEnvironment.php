<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\ResidenceEnvironment;

trait HasVerticalHeaderEnvironment
{
    public function getVerticalHeader(): array
    {
        return ResidenceEnvironment::options();
    }

    public function getVerticalHeaderKey(): string
    {
        return 'environment';
    }
}
