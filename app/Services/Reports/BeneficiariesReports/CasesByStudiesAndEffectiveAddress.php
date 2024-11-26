<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasHorizontalSubHeaderEnvironment;
use App\Concerns\Reports\HasVerticalHeaderStudies;
use App\Concerns\Reports\InteractWithEffectiveAddressAndBeneficiaryDetails;
use App\Interfaces\ReportGenerator;

class CasesByStudiesAndEffectiveAddress extends BaseGenerator implements ReportGenerator
{
    use HasHorizontalSubHeaderEnvironment;
    use HasVerticalHeaderStudies;
    use InteractWithEffectiveAddressAndBeneficiaryDetails;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.studies'),
            __('report.headers.cases_by_effective_address'),
            __('report.headers.subtotal'),
        ];
    }

    public function getSelectedFields(): array|string
    {
        return ['studies', 'environment'];
    }
}
