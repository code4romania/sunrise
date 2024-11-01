<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasHorizontalSubHeaderEnvironment;
use App\Concerns\Reports\HasVerticalHeaderOccupation;
use App\Concerns\Reports\InteractWithEffectiveAddressAndBeneficiaryDetails;
use App\Interfaces\ReportGenerator;

class CasesByOccupationAndEffectiveAddress extends BaseGenerator implements ReportGenerator
{
    use HasHorizontalSubHeaderEnvironment;
    use HasVerticalHeaderOccupation;
    use InteractWithEffectiveAddressAndBeneficiaryDetails;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.occupation'),
            __('report.headers.cases_by_effective_address'),
            __('report.headers.subtotal'),
        ];
    }

    public function getSelectedFields(): array|string
    {
        return ['occupation', 'environment'];
    }
}
