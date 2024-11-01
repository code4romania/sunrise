<?php

declare(strict_types=1);

namespace App\Services\Reports;

use App\Enums\AgeInterval;
use App\Enums\AgeInterval2;
use App\Enums\AggressorRelationship;
use App\Enums\BeneficiarySegmentationByAge;
use App\Enums\Citizenship;
use App\Enums\CivilStatus;
use App\Enums\Ethnicity;
use App\Enums\Frequency;
use App\Enums\Gender;
use App\Enums\HomeOwnership;
use App\Enums\Income;
use App\Enums\Occupation;
use App\Enums\PresentationMode;
use App\Enums\ReportType;
use App\Enums\ResidenceEnvironment;
use App\Enums\Studies;
use App\Enums\Violence;
use App\Models\Beneficiary;
use App\Models\ReferringInstitution;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

// TODO: remove this class after migrate in V2
class Beneficiaries
{
    protected Builder | null $query = null;

    protected ReportType | null $reportType = null;

    protected string | null $startDate = null;

    protected string | null $endDate = null;

    protected Collection | null $reportData = null;

    protected string | null $fields = null;

    protected array $header = [];

    protected array $subHeader = [];

    protected ?string $subHeaderKey = null;

    protected array $verticalHeader = [];

    protected ?string $verticalHeaderKey = null;

    protected array $verticalSubHeader = [];

    protected ?string $verticalSubHeaderKey = null;

    protected bool | null $showMissingValues = false;

    public function composeReport(): void
    {
        $this->header = [];
        $this->subHeader = [];
        $this->verticalHeader = [];
        $this->verticalSubHeader = [];
        $this->fields = null;

        if (! $this->reportType) {
            return;
        }

        $this->query = Beneficiary::query();

        $this->setHeadersAndKeys();

        $this->setSelectFields();

        $this->addRelatedTables();

        $this->addConditions();

        $this->setGroupBy();

        $this->setReportData();
    }

    public function getReportType(): ?ReportType
    {
        return $this->reportType;
    }

    public function getReportData(): Collection
    {
        return $this->reportData ?? collect();
    }

    public function getHorizontalHeader(): array
    {
        return $this->header;
    }

    public function getHorizontalSubHeader(): array
    {
        return $this->subHeader;
    }

    public function getSubHeaderKey(): ?string
    {
        return $this->subHeaderKey;
    }

    public function getVerticalHeader(): array
    {
        return $this->verticalHeader;
    }

    public function getVerticalHeaderKey(): ?string
    {
        return $this->verticalHeaderKey;
    }

    public function getVerticalSubHeader(): array
    {
        return $this->verticalSubHeader;
    }

    public function getVerticalSubHeaderKey(): ?string
    {
        return $this->verticalSubHeaderKey;
    }

    public function setReportType(ReportType | string | null $reportType): self
    {
        $this->reportType = $reportType;

        return $this;
    }

    public function setStartDate(?string $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function setEndDate(?string $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function setShowMissingValue(?bool $showMissingValue): self
    {
        $this->showMissingValues = $showMissingValue;

        return $this;
    }

    public function setHeadersAndKeys(): void
    {
        switch ($this->reportType) {
            case ReportType::CASES_BY_AGE_GENDER_AND_LEGAL_ADDRESS:
                $this->header = [
                    __('report.headers.gender_and_legal_address'),
                    __('report.headers.cases_by_age_groups'),
                    __('report.headers.total'),
                ];

                $this->setSubHeaderFor('age_group');

                $this->setVerticalHeaderFor('gender');

                $this->setVerticalSubHeaderFor('legal_residence_environment');

                break;

            case ReportType::CASES_BY_AGE_GENDER_AND_EFFECTIVE_ADDRESS:
                $this->header = [
                    __('report.headers.gender_and_effective_address'),
                    __('report.headers.cases_by_age_groups'),
                    __('report.headers.total'),
                ];

                $this->setSubHeaderFor('age_group');

                $this->setVerticalHeaderFor('gender');

                $this->setVerticalSubHeaderFor('effective_residence_environment');

                break;

            case ReportType::CASES_BY_OCCUPATION:
                $this->header = [
                    __('report.headers.occupation'),
                    __('report.headers.case_distribution'),
                ];

                $this->setVerticalHeaderFor('occupation');

                break;

            case ReportType::CASES_BY_OCCUPATION_AND_EFFECTIVE_ADDRESS:
                $this->header = [
                    __('report.headers.occupation'),
                    __('report.headers.cases_by_effective_address'),
                    __('report.headers.subtotal'),
                ];

                $this->setSubHeaderFor('effective_residence_environment');

                $this->setVerticalHeaderFor('occupation');

                break;

            case ReportType::CASES_BY_OCCUPATION_EFFECTIVE_ADDRESS_AND_GENDER:
                $this->header = [
                    __('report.headers.occupation_and_effective_address'),
                    __('report.headers.cases_by_gender'),
                    __('report.headers.total'),
                ];

                $this->setSubHeaderFor('gender');

                $this->setVerticalHeaderFor('occupation');

                $this->setVerticalSubHeaderFor('effective_residence_environment');

                break;

            case ReportType::CASES_BY_INCOME:
                $this->header = [
                    __('report.headers.income'),
                    __('report.headers.case_distribution'),
                ];

                $this->setVerticalHeaderFor('income');

                break;

            case ReportType::CASES_BY_INCOME_AND_EFFECTIVE_ADDRESS:
                $this->header = [
                    __('report.headers.income'),
                    __('report.headers.cases_by_effective_address'),
                    __('report.headers.subtotal'),
                ];

                $this->setSubHeaderFor('effective_residence_environment');

                $this->setVerticalHeaderFor('income');

                break;

            case ReportType::CASES_BY_INCOME_EFFECTIVE_ADDRESS_AND_GENDER:
                $this->header = [
                    __('report.headers.income_and_effective_address'),
                    __('report.headers.cases_by_gender'),
                    __('report.headers.total'),
                ];

                $this->setSubHeaderFor('gender');

                $this->setVerticalHeaderFor('income');

                $this->setVerticalSubHeaderFor('effective_residence_environment');

                break;

            case ReportType::CASES_BY_HOME_OWNERSHIP:
                $this->header = [
                    __('report.headers.home_ownership'),
                    __('report.headers.case_distribution'),
                ];

                $this->setVerticalHeaderFor('homeownership');

                break;

            case ReportType::CASES_BY_HOME_OWNERSHIP_AND_EFFECTIVE_ADDRESS:
                $this->header = [
                    __('report.headers.home_ownership'),
                    __('report.headers.cases_by_effective_address'),
                    __('report.headers.subtotal'),
                ];

                $this->setSubHeaderFor('effective_residence_environment');

                $this->setVerticalHeaderFor('homeownership');

                break;

            case ReportType::CASES_BY_HOME_OWNERSHIP_EFFECTIVE_ADDRESS_AND_GENDER:
                $this->header = [
                    __('report.headers.home_ownership_and_effective_address'),
                    __('report.headers.cases_by_gender'),
                    __('report.headers.total'),
                ];

                $this->setSubHeaderFor('gender');

                $this->setVerticalHeaderFor('homeownership');

                $this->setVerticalSubHeaderFor('effective_residence_environment');

                break;

            case ReportType::CASES_BY_AGGRESSOR_RELATIONSHIP:
                $this->header = [
                    __('report.headers.aggressor_relationship'),
                    __('report.headers.case_distribution'),
                ];

                $this->setVerticalHeaderFor('relationship');

                break;

            case ReportType::CASES_BY_AGGRESSOR_RELATIONSHIP_AND_AGE:
                $this->header = [
                    __('report.headers.aggressor_relationship'),
                    __('report.headers.cases_by_age_segmentation'),
                    __('report.headers.subtotal'),
                ];

                $this->setSubHeaderFor('segmentation_by_age');

                $this->setVerticalHeaderFor('relationship');

                break;

            case ReportType::CASES_BY_AGGRESSOR_RELATIONSHIP_GENDER_AND_AGE:
                $this->header = [
                    __('report.headers.aggressor_relationship_and_age'),
                    __('report.headers.cases_by_gender'),
                    __('report.headers.total'),
                ];

                $this->setSubHeaderFor('gender');

                $this->setVerticalHeaderFor('relationship');

                $this->setVerticalSubHeaderFor('segmentation_by_age');

                break;

            case ReportType::CASES_BY_PRIMARY_VIOLENCE_TYPE:
                $this->header = [
                    __('report.headers.primary_violence'),
                    __('report.headers.case_distribution'),
                ];

                $this->setVerticalHeaderFor('violence_primary_type');

                break;

            case ReportType::CASES_BY_VIOLENCE_TYPES:
                $this->header = [
                    __('report.headers.violence_types'),
                    __('report.headers.case_distribution'),
                ];

                $this->setVerticalHeaderFor('violence_types');

                break;

            case ReportType::CASES_BY_LEGAL_ADDRESS:
                $this->header = [
                    __('report.headers.legal_address'),
                    __('report.headers.case_distribution'),
                ];

                $this->setVerticalHeaderFor('legal_residence_environment');

                break;

            case ReportType::CASES_BY_EFFECTIVE_ADDRESS:
                $this->header = [
                    __('report.headers.effective_address'),
                    __('report.headers.case_distribution'),
                ];

                $this->setVerticalHeaderFor('effective_residence_environment');

                break;

            case ReportType::CASES_BY_PRIMARY_VIOLENCE_TYPE_AND_AGE:
                $this->header = [
                    __('report.headers.primary_violence'),
                    __('report.headers.cases_by_age_segmentation'),
                    __('report.headers.subtotal'),
                ];

                $this->setSubHeaderFor('segmentation_by_age');

                $this->setVerticalHeaderFor('violence_primary_type');

                break;

            case ReportType::CASES_BY_VIOLENCE_FREQUENCY:
                $this->header = [
                    __('report.headers.frequency_violence'),
                    __('report.headers.case_distribution'),
                ];

                $this->setVerticalHeaderFor('frequency_violence');

                break;

            case ReportType::CASES_BY_PRIMARY_VIOLENCE_FREQUENCY_AND_AGE:
                $this->header = [
                    __('report.headers.primary_violence_and_age'),
                    __('report.headers.cases_by_frequency_violence'),
                    __('report.headers.subtotal'),
                ];

                $this->setSubHeaderFor('frequency_violence');

                $this->setVerticalHeaderFor('violence_primary_type');

                $this->setVerticalSubHeaderFor('segmentation_by_age');

                break;

            case ReportType::CASES_BY_PRESENTATION_MODE:
                $this->header = [
                    __('report.headers.presentation_mode'),
                    __('report.headers.case_distribution'),
                ];

                $this->setVerticalHeaderFor('presentation_mode');

                break;

            case ReportType::CASES_BY_REFERRING_INSTITUTION:
                $this->header = [
                    __('report.headers.referring_institution'),
                    __('report.headers.case_distribution'),
                ];

                $this->setVerticalHeaderFor('referring_institution_id');

                break;
        }
    }

    private function setSubHeaderFor(string $field): void
    {
        switch ($field) {
            case 'age_group':
                $this->subHeader = AgeInterval::options();
                break;

            case 'effective_residence_environment':
                $this->subHeader = ResidenceEnvironment::options();
                break;

            case 'gender':
                $this->subHeader = Gender::options();
                break;

            case 'segmentation_by_age':
                $this->subHeader = BeneficiarySegmentationByAge::options();
                break;

            case 'age_group_2':
                $this->subHeader = AgeInterval2::options();
                break;

            case 'frequency_violence':
                $this->subHeader = Frequency::options();
                break;
        }

        $this->subHeaderKey = \in_array($field, ['age_group_2', 'segmentation_by_age']) ?
            'age_group' : $field;

        if ($this->showMissingValues) {
            if ($this->subHeaderKey == 'age_group') {
                $this->subHeader['unknown'] = __('report.headers.missing_values');

                return;
            }

            $this->subHeader[null] = __('report.headers.missing_values');
        }
    }

    public function setVerticalHeaderFor(string $field): void
    {
        switch ($field) {
            case 'legal_residence_environment':
            case 'effective_residence_environment':
                $this->verticalHeader = ResidenceEnvironment::options();
                break;

            case 'age_group':
                $this->verticalHeader = AgeInterval::options();
                break;

            case 'violence_primary_type':
            case 'violence_types':
                $this->verticalHeader = Violence::options();
                break;

            case 'relationship':
                $this->verticalHeader = AggressorRelationship::options();
                break;

            case 'civil_status':
                $this->verticalHeader = CivilStatus::options();
                break;

            case 'homeownership':
                $this->verticalHeader = HomeOwnership::options();
                break;

            case 'income':
                $this->verticalHeader = Income::options();
                break;

            case 'occupation':
                $this->verticalHeader = Occupation::options();
                break;

            case 'studies':
                $this->verticalHeader = Studies::options();
                break;

            case 'gender':
                $this->verticalHeader = Gender::options();
                break;

            case 'ethnicity':
                $this->verticalHeader = Ethnicity::options();
                break;

            case 'citizenship':
                $this->verticalHeader = Citizenship::options();
                break;

            case 'frequency_violence':

                $this->verticalHeader = Frequency::options();
                break;

            case 'presentation_mode':
                $this->verticalHeader = PresentationMode::options();
                break;

            case 'referring_institution_id':
                $this->verticalHeader = ReferringInstitution::all()->pluck('name', 'id')->toArray();
                break;

            case 'segmentation_by_age':
                $this->verticalHeader = BeneficiarySegmentationByAge::options();
                break;
        }

        $field = $field === 'segmentation_by_age' ? 'age_group' : $field;

        $this->verticalHeaderKey = $field;

        if ($this->showMissingValues) {
            if ($this->verticalHeaderKey == 'age_group') {
                $this->verticalHeader['unknown'] = __('report.headers.missing_values');

                return;
            }

            $this->verticalHeader[null] = __('report.headers.missing_values');
        }
    }

    private function setVerticalSubHeaderFor(string $field): void
    {
        switch ($field) {
            case 'segmentation_by_age':
                $this->verticalSubHeader = BeneficiarySegmentationByAge::options();
                break;

            case 'legal_residence_environment':
            case 'effective_residence_environment':
                $this->verticalSubHeader = ResidenceEnvironment::options();
                break;
        }

        $this->verticalSubHeaderKey = \in_array($field, ['age_group_2', 'segmentation_by_age']) ?
            'age_group' : $field;

        if ($this->showMissingValues) {
            if ($this->verticalSubHeaderKey == 'age_group') {
                $this->verticalSubHeader['unknown'] = __('report.headers.missing_values');

                return;
            }

            $this->verticalSubHeader[null] = __('report.headers.missing_values');
        }
    }

    public function setSelectFields(): void
    {
        if ($this->reportType === ReportType::CASES_BY_VIOLENCE_TYPES) {
            $this->query->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(violences.violence_types, "$[*]")) as violence_types, COUNT(beneficiaries.id) as total_cases');
        } else {
            $this->query->selectRaw('COUNT(*) as total_cases');
        }

        switch ($this->reportType) {
            case ReportType::CASES_BY_AGE_GENDER_AND_LEGAL_ADDRESS:
            case ReportType::CASES_BY_AGE_GENDER_AND_EFFECTIVE_ADDRESS:
                $this->query->selectRaw("CASE
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
                        END as age_group");
                break;

            case ReportType::CASES_BY_PRIMARY_VIOLENCE_TYPE_AND_AGE:
            case ReportType::CASES_BY_PRIMARY_VIOLENCE_FREQUENCY_AND_AGE:
            case ReportType::CASES_BY_AGGRESSOR_RELATIONSHIP_GENDER_AND_AGE:
            case ReportType::CASES_BY_AGGRESSOR_RELATIONSHIP_AND_AGE:
                $this->query->selectRaw("CASE
                            WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) < 17 THEN 'minor'
                            WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) > 18 THEN 'major'
                            ELSE 'unknown'
                        END as age_group");
                break;
        }

        if ($this->subHeaderKey && $this->subHeaderKey !== 'age_group') {
            $this->query->selectRaw($this->getFieldNameForHeaderKey($this->subHeaderKey));
        }

        if ($this->verticalHeaderKey && $this->verticalHeaderKey !== 'age_group') {
            $this->query->selectRaw($this->getFieldNameForHeaderKey($this->verticalHeaderKey));
        }

        if ($this->verticalSubHeaderKey && $this->verticalSubHeaderKey !== 'age_group') {
            $this->query->selectRaw($this->getFieldNameForHeaderKey($this->verticalSubHeaderKey));
        }
    }

    public function setGroupBy(): void
    {
        if ($this->verticalHeaderKey) {
            $this->query->groupBy($this->verticalHeaderKey);
        }

        if ($this->verticalSubHeaderKey) {
            $this->query->groupBy($this->verticalSubHeaderKey);
        }

        if ($this->subHeaderKey) {
            $this->query->groupBy($this->subHeaderKey);
        }
    }

    private function addRelatedTables(): void
    {
        switch ($this->reportType) {
            case ReportType::CASES_BY_AGGRESSOR_RELATIONSHIP:
            case ReportType::CASES_BY_AGGRESSOR_RELATIONSHIP_AND_AGE:
            case ReportType::CASES_BY_AGGRESSOR_RELATIONSHIP_GENDER_AND_AGE:
                $this->query->join('aggressors', 'beneficiary_id', '=', 'beneficiaries.id');
                break;

            case ReportType::CASES_BY_PRIMARY_VIOLENCE_TYPE:
            case ReportType::CASES_BY_PRIMARY_VIOLENCE_TYPE_AND_AGE:
            case ReportType::CASES_BY_PRIMARY_VIOLENCE_FREQUENCY_AND_AGE:
            case ReportType::CASES_BY_VIOLENCE_FREQUENCY:
            case ReportType::CASES_BY_VIOLENCE_TYPES:
                $this->query->join('violences', 'beneficiary_id', '=', 'beneficiaries.id');
                break;
        }
    }

    private function addConditions(): void
    {
        if (! $this->showMissingValues) {
            if ($this->subHeaderKey) {
                $this->query->whereNotNull($this->getFieldNameForHeaderKey($this->subHeaderKey));
            }

            if ($this->verticalHeaderKey) {
                $this->query->whereNotNull($this->getFieldNameForHeaderKey($this->verticalHeaderKey));
            }

            if ($this->verticalSubHeaderKey) {
                $this->query->whereNotNull($this->getFieldNameForHeaderKey($this->verticalSubHeaderKey));
            }
        }

        if ($this->endDate) {
            $this->query->where('beneficiaries.created_at', '<=', $this->endDate);
        }

        if ($this->startDate) {
//            $this->query->where('close_files.date', '>=', $this->startDate);
        }
    }

    private function getFieldNameForHeaderKey(string $headerKey): string
    {
        if ($headerKey === 'age_group') {
            return 'birthdate';
        }

        if ($headerKey === 'gender') {
            return 'beneficiaries.gender';
        }

        return $headerKey;
    }

    private function setReportData(): void
    {
        $this->reportData = $this->query
            ->get();

        if ($this->reportType === ReportType::CASES_BY_VIOLENCE_TYPES) {
            $data = $this->reportData;
            $this->reportData = collect();

            foreach ($data as $row) {
                $violenceTypes = json_decode($row->violence_types);
                if (empty($violenceTypes) && $this->showMissingValues) {
                    $this->addCasesForViolenceTypes($row);
                }
                foreach ($violenceTypes as $violenceType) {
                    $this->addCasesForViolenceTypes($row, $violenceType);
                }
            }
        }
    }

    private function addCasesForViolenceTypes($row, ?string $violenceType = null): void
    {
        $element = $this->reportData->filter(fn ($item) => $item->violence_types === $violenceType)->first();
        if ($element) {
            $element->total_cases += $row->total_cases;

            return;
        }
        $element = clone $row;
        $element->violence_types = $violenceType;
        $this->reportData->add($element);
    }
}
