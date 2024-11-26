<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderIncome;
use App\Concerns\Reports\HasVerticalSubHeaderEnvironment;
use App\Concerns\Reports\InteractWithEffectiveAddressAndBeneficiaryDetails;
use App\Enums\Gender;
use App\Interfaces\ReportGenerator;

class CasesByIncomeEffectiveAddressAndGender extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderIncome;
    use HasVerticalSubHeaderEnvironment;
    use InteractWithEffectiveAddressAndBeneficiaryDetails;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.income_and_effective_address'),
            __('report.headers.cases_by_gender'),
            __('report.headers.total'),
        ];
    }

    public function getHorizontalSubHeader(): ?array
    {
        $header = Gender::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header[null] = __('report.headers.missing_values');

        return $header;
    }

    public function getHorizontalSubHeaderKey(): ?string
    {
        return 'gender';
    }

    public function getSelectedFields(): array|string
    {
        return ['income', 'gender', 'environment'];
    }
}
