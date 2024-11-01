<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\Studies;

trait HasVerticalHeaderStudies
{
    public function getVerticalHeader(): array
    {
        return Studies::options();
    }

    public function getVerticalHeaderKey(): string
    {
        return 'studies';
    }
}
