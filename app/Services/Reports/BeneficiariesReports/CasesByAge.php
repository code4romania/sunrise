<?php

declare(strict_types=1);

namespace App\Services\Reports\BeneficiariesReports;

use App\Enums\AgeInterval;
use App\Interfaces\ReportGenerator;

class CasesByAge extends BaseGenerator implements ReportGenerator
{
    public function getHorizontalHeader(): array
    {
        return [
            __('report.headers.age'),
            __('report.headers.case_distribution'),
        ];
    }

    public function getVerticalHeader(): array
    {
        $header = AgeInterval::options();

        if (! $this->showMissingValues) {
            return $header;
        }

        $header['unknown'] = __('report.headers.missing_values');

        return $header;
    }

    public function getVerticalHeaderKey(): string
    {
        return 'age_group';
    }

    public function getSelectedFields(): array|string
    {
        return "CASE
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) < 1 THEN 'under_1_year'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 1 AND 2 THEN 'between_1_and_2_years'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 3 AND 6 THEN 'between_3_and_6_years'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 7 AND 9 THEN 'between_7_and_9_years'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 10 AND 13 THEN 'between_10_and_13_years'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 14 AND 17 THEN 'between_14_and_17_years'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 18 AND 25 THEN 'between_18_and_25_years'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 26 AND 35 THEN 'between_26_and_35_years'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 36 AND 45 THEN 'between_36_and_45_years'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 46 AND 55 THEN 'between_46_and_55_years'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 56 AND 65 THEN 'between_56_and_65_years'
                WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) > 65 THEN 'over_65_years'
                ELSE 'unknown'
            END as age_group";
    }
}
