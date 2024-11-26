<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderRelationship;
use App\Enums\BeneficiarySegmentationByAge;
use App\Enums\Gender;
use App\Interfaces\ReportGenerator;

class CasesByAggressorRelationshipAndAgeAndGender extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderRelationship;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.aggressor_relationship_and_age'),
            __('report.headers.cases_by_gender'),
            __('report.headers.total'),
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

    public function getVerticalSubHeader(): ?array
    {
        $header = BeneficiarySegmentationByAge::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header['unknown'] = __('report.headers.missing_values');

        return $header;
    }

    public function getVerticalSubHeaderKey(): ?string
    {
        return 'age_group';
    }

    public function getSelectedFields(): array|string
    {
        return [
            "CASE
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) < 17 THEN 'minor'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) > 18 THEN 'major'
                ELSE 'unknown'
            END as age_group",
            'relationship',
            'beneficiaries.gender',
        ];
    }

    public function addRelatedTables(): void
    {
        $this->query->join('aggressors', 'beneficiaries.id', '=', 'aggressors.beneficiary_id');
    }
}
