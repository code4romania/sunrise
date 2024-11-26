<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Enums\Citizenship;
use App\Interfaces\ReportGenerator;

class CasesByCitizenship extends BaseGenerator implements ReportGenerator
{
    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.citizenship'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getVerticalHeader(): array
    {
        $header = Citizenship::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header[null] = __('report.headers.missing_values');

        return $header;
    }

    public function getVerticalHeaderKey(): string
    {
        return 'citizenship';
    }

    public function getSelectedFields(): array|string
    {
        return 'citizenship';
    }
}
