<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\Income;

trait HasVerticalHeaderIncome
{
    public function getVerticalHeader(): array
    {
        return Income::options();
    }

    public function getVerticalHeaderKey(): string
    {
        return 'income';
    }
}
