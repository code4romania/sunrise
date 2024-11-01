<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderCivilStatus;
use App\Enums\Gender;
use App\Interfaces\ReportGenerator;

class CasesByCivilStatusAndGender extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderCivilStatus;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.civil_status'),
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
        return ['civil_status', 'gender'];
    }
}
