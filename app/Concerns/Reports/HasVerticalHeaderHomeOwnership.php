<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\HomeOwnership;

trait HasVerticalHeaderHomeOwnership
{
    public function getVerticalHeader(): array
    {
        return HomeOwnership::options();
    }

    public function getVerticalHeaderKey(): string
    {
        return 'homeownership';
    }
}
