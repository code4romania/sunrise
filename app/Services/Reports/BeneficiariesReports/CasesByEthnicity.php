<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Enums\Ethnicity;
use App\Interfaces\ReportGenerator;

class CasesByEthnicity extends BaseGenerator implements ReportGenerator
{
    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.ethnicity'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getVerticalHeader(): array
    {
        return Ethnicity::options();
    }

    public function getVerticalHeaderKey(): string
    {
        return 'ethnicity';
    }

    public function getSelectedFields(): array|string
    {
        return 'ethnicity';
    }
}
