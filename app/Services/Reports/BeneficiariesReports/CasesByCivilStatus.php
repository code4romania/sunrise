<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderCivilStatus;
use App\Interfaces\ReportGenerator;

class CasesByCivilStatus extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderCivilStatus;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.civil_status'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getSelectedFields(): array|string
    {
        return 'civil_status';
    }
}
