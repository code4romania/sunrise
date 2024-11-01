<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderHomeOwnership;
use App\Concerns\Reports\InteractWithEffectiveAddressAndBeneficiaryDetails;
use App\Enums\Gender;
use App\Enums\ResidenceEnvironment;
use App\Interfaces\ReportGenerator;

class CasesByHomeOwnershipEffectiveAddressAndGender extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderHomeOwnership;
    use InteractWithEffectiveAddressAndBeneficiaryDetails;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.home_ownership_and_effective_address'),
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

    public function getVerticalSubHeader(): ?array
    {
        $header = ResidenceEnvironment::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header[null] = __('report.headers.missing_values');

        return $header;
    }

    public function getVerticalSubHeaderKey(): ?string
    {
        return 'environment';
    }

    public function getSelectedFields(): array|string
    {
        return ['homeownership', 'environment', 'gender'];
    }
}
