<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasHorizontalSubHeaderEnvironment;
use App\Concerns\Reports\HasVerticalHeaderIncome;
use App\Concerns\Reports\InteractWithEffectiveAddressAndBeneficiaryDetails;
use App\Interfaces\ReportGenerator;

class CasesByIncomeAndEffectiveAddress extends BaseGenerator implements ReportGenerator
{
    use HasHorizontalSubHeaderEnvironment;
    use HasVerticalHeaderIncome;
    use InteractWithEffectiveAddressAndBeneficiaryDetails;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.income'),
            __('report.headers.cases_by_effective_address'),
            __('report.headers.subtotal'),
        ];
    }

    public function getSelectedFields(): array|string
    {
        return ['income', 'environment'];
    }
}
