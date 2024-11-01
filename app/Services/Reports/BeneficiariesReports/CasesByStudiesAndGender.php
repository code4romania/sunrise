<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderStudies;
use App\Enums\Gender;
use App\Interfaces\ReportGenerator;

class CasesByStudiesAndGender extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderStudies;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.studies'),
            __('report.headers.cases_by_gender'),
            __('report.headers.subtotal'),
        ];
    }

    public function getHorizontalSubHeader(): ?array
    {
        return Gender::options();
    }

    public function getHorizontalSubHeaderKey(): ?string
    {
        return 'gender';
    }

    public function getSelectedFields(): array|string
    {
        return ['gender', 'studies'];
    }

    public function addRelatedTables(): void
    {
        $this->query->join('beneficiary_details', 'beneficiary_details.beneficiary_id', '=', 'beneficiaries.id');
    }
}
