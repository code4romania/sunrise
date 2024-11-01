<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderEnvironment;
use App\Concerns\Reports\InteractWithLegalAddress;
use App\Interfaces\ReportGenerator;

class CasesByLegalAddress extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderEnvironment;
    use InteractWithLegalAddress;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.legal_address'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getSelectedFields(): array|string
    {
        return 'environment';
    }
}
