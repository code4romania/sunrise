<?php

declare(strict_types=1);

namespace App\Concerns\Reports;

use App\Enums\Income;

trait HasVerticalHeaderIncome
{
    public function getVerticalHeader(): array
    {
        $header = Income::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header[null] = __('report.headers.missing_values');

        return $header;
    }

    public function getVerticalHeaderKey(): string
    {
        return 'income';
    }
}
