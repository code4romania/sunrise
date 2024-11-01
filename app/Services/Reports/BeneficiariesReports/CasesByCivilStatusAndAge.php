<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Concerns\Reports\HasVerticalHeaderCivilStatus;
use App\Enums\AgeInterval2;
use App\Interfaces\ReportGenerator;

class CasesByCivilStatusAndAge extends BaseGenerator implements ReportGenerator
{
    use HasVerticalHeaderCivilStatus;

    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.civil_status'),
            __('report.headers.cases_by_age_groups'),
            __('report.headers.subtotal'),
        ];
    }

    public function getHorizontalSubHeader(): ?array
    {
        return AgeInterval2::options();
    }

    public function getHorizontalSubHeaderKey(): ?string
    {
        return 'age_group';
    }

    public function getSelectedFields(): array|string
    {
        return [
            "CASE
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 14 AND 17 THEN 'between_14_and_17_years'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 18 AND 25 THEN 'between_18_and_25_years'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 26 AND 35 THEN 'between_26_and_35_years'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 36 AND 45 THEN 'between_36_and_45_years'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 46 AND 55 THEN 'between_46_and_55_years'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 56 AND 65 THEN 'between_56_and_65_years'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) > 65 THEN 'over_65_years'
                ELSE 'unknown'
            END as age_group",
            'civil_status',
        ];
    }
}
