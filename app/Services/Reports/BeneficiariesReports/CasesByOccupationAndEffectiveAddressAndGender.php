<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderOccupation;
use App\Concerns\Reports\HasVerticalSubHeaderEnvironment;
use App\Concerns\Reports\InteractWithEffectiveAddressAndBeneficiaryDetails;
use App\Enums\Gender;
use App\Interfaces\ReportGenerator;

class CasesByOccupationAndEffectiveAddressAndGender extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderOccupation;
    use HasVerticalSubHeaderEnvironment;
    use InteractWithEffectiveAddressAndBeneficiaryDetails;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.occupation_and_effective_address'),
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
        return ['occupation', 'gender', 'environment'];
    }
}
