<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderEnvironment;
use App\Concerns\Reports\InteractWithEffectiveAddress;
use App\Interfaces\ReportGenerator;

class CasesByEffectiveAddress extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderEnvironment;
    use InteractWithEffectiveAddress;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.effective_address'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getSelectedFields(): array|string
    {
        return 'environment';
    }
}
