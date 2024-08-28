<?php

declare(strict_types=1);

namespace App\Forms\Components;

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
use Filament\Forms\Components\Component;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ReportTable extends Component
{
    protected string $view = 'forms.components.report-table';

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

    public static function make(string | null $id = null): static
    {
        $static = app(static::class, ['id' => $id]);
        $static->configure();

        return $static;
    }

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

        $this->reportData = $this->query
            ->get();

        // todo map data for violence types
        debug($this->reportType, $this->reportData);
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
                    'Gen / Domiciliu legal',
                    'Distribuţia cazurilor pe grupe de vârstă',
                    'Total general cazuri',
                ];

                $this->setSubHeaderFor('age_group');

                $this->setVerticalHeaderFor('gender');

                $this->setVerticalSubHeaderFor('legal_residence_environment');

                break;

            case ReportType::CASES_BY_AGE_GENDER_AND_EFFECTIVE_ADDRESS:
                $this->header = [
                    'Gen / Domiciliu legal',
                    'Distribuţia cazurilor pe grupe de vârstă',
                    'Total general cazuri',
                ];

                $this->setSubHeaderFor('age_group');

                $this->setVerticalHeaderFor('gender');

                $this->setVerticalSubHeaderFor('effective_residence_environment');

                break;

            case ReportType::CASES_BY_ETHNICITY:
                $this->header = [
                    'Etnie',
                    'Distribuția cazurilor',
                ];

                $this->setVerticalHeaderFor('ethnicity');

                break;

            case ReportType::CASES_BY_CITIZENSHIP:
                $this->header = [
                    'Cetățenie',
                    'Distribuția cazurilor',
                ];

                $this->setVerticalHeaderFor('citizenship');

                break;

            case ReportType::CASES_BY_STUDIES:
                $this->header = [
                    'Nivel de studii',
                    'Distribuția cazurilor',
                ];

                $this->setVerticalHeaderFor('studies');

                break;

            case ReportType::CASES_BY_STUDIES_AND_EFFECTIVE_ADDRESS:
                $this->header = [
                    'Nivel de studii',
                    'Distribuţia cazurilor după domiciliul efectiv al victimei',
                    'Subtotal cazuri',
                ];

                $this->setSubHeaderFor('effective_residence_environment');

                $this->setVerticalHeaderFor('studies');

                break;

            case ReportType::CASES_BY_STUDIES_AND_GENDER:
                $this->header = [
                    'Nivel de studii',
                    'Distribuţia cazurilor după genul victimei',
                    'Subtotal cazuri',
                ];

                $this->setSubHeaderFor('gender');

                $this->setVerticalHeaderFor('studies');

                break;

            case ReportType::CASES_BY_STUDIES_AND_AGE:
                $this->header = [
                    'Nivel de studii',
                    'Distribuţia cazurilor după vârsta victimei (minor/major)',
                    'Subtotal cazuri',
                ];

                $this->setSubHeaderFor('segmentation_by_age');

                $this->setVerticalHeaderFor('studies');

                break;

            case ReportType::CASES_BY_OCCUPATION:
                $this->header = [
                    'Ocupație',
                    'Distribuția cazurilor',
                ];

                $this->setVerticalHeaderFor('occupation');

                break;

            case ReportType::CASES_BY_OCCUPATION_AND_EFFECTIVE_ADDRESS:
                $this->header = [
                    'Nivel de studii',
                    'Distribuția cazurilor',
                    'Subtotal cazuri',
                ];

                $this->setSubHeaderFor('effective_residence_environment');

                $this->setVerticalHeaderFor('occupation');

                break;

            case ReportType::CASES_BY_OCCUPATION_EFFECTIVE_ADDRESS_AND_GENDER:
                $this->header = [
                    'Ocupație / Domiciliu efectiv',
                    'Distribuţia cazurilor după genul victimei',
                    'Total general cazuri',
                ];

                $this->setSubHeaderFor('gender');

                $this->setVerticalHeaderFor('occupation');

                $this->setVerticalSubHeaderFor('effective_residence_environment');

                break;

            case ReportType::CASES_BY_INCOME:
                $this->header = [
                    'Încadrare în venit',
                    'Distribuția cazurilor',
                ];

                $this->setVerticalHeaderFor('income');

                break;

            case ReportType::CASES_BY_INCOME_AND_EFFECTIVE_ADDRESS:
                $this->header = [
                    'Încadrare în venit / Domiciliu efectiv',
                    'Distribuţia cazurilor după genul victimei',
                    'Total general cazuri',
                ];

                $this->setSubHeaderFor('effective_residence_environment');

                $this->setVerticalHeaderFor('income');

                break;

            case ReportType::CASES_BY_INCOME_EFFECTIVE_ADDRESS_AND_GENDER:
                $this->header = [
                    'Nivel de studii',
                    'Distribuția cazurilor',
                    'Subtotal cazuri',
                ];

                $this->setSubHeaderFor('gender');

                $this->setVerticalHeaderFor('income');

                $this->setVerticalSubHeaderFor('effective_residence_environment');

                break;

            case ReportType::CASES_BY_HOME_OWNERSHIP:
                $this->header = [
                    'Dreptul de proprietate asupra locuinței primare',
                    'Distribuția cazurilor',
                ];

                $this->setVerticalHeaderFor('homeownership');

                break;

            case ReportType::CASES_BY_HOME_OWNERSHIP_AND_EFFECTIVE_ADDRESS:
                $this->header = [
                    'Dreptul de proprietate asupra locuinței primare',
                    'Distribuţia cazurilor după domiciliul efectiv al victimei',
                    'Subtotal cazuri',
                ];

                $this->setSubHeaderFor('effective_residence_environment');

                $this->setVerticalHeaderFor('homeownership');

                break;

            case ReportType::CASES_BY_HOME_OWNERSHIP_EFFECTIVE_ADDRESS_AND_GENDER:
                $this->header = [
                    'Încadrare în venit / Domiciliu efectiv',
                    'Distribuţia cazurilor după genul victimei',
                    'Total general cazuri',
                ];

                $this->setSubHeaderFor('gender');

                $this->setVerticalHeaderFor('homeownership');

                $this->setVerticalSubHeaderFor('effective_residence_environment');

                break;

            case ReportType::CASES_BY_CIVIL_STATUS:
                $this->header = [
                    'Stare civilă',
                    'Distribuția cazurilor',
                ];

                $this->setVerticalHeaderFor('civil_status');

                break;

            case ReportType::CASES_BY_CIVIL_STATUS_AND_GENDER:
                $this->header = [
                    'Stare civilă',
                    'Distribuţia cazurilor după genul victimei',
                    'Total general cazuri',
                ];

                $this->setSubHeaderFor('gender');

                $this->setVerticalHeaderFor('civil_status');

                break;

            case ReportType::CASES_BY_CIVIL_STATUS_AND_AGE:
                $this->header = [
                    'Stare civilă',
                    'Distribuţia cazurilor după grupa de vârstă',
                    'Subtotal cazuri',
                ];

                $this->setSubHeaderFor('age_group_2');

                $this->setVerticalHeaderFor('civil_status');

                break;

            case ReportType::CASES_BY_AGGRESSOR_RELATIONSHIP:
                $this->header = [
                    'Relația cu agresorul',
                    'Distribuția cazurilor',
                ];

                $this->setVerticalHeaderFor('relationship');

                break;

            case ReportType::CASES_BY_AGGRESSOR_RELATIONSHIP_AND_AGE:
                $this->header = [
                    'Relația cu agresorul',
                    'Distribuţia cazurilor după vârsta victimei (minor/major)',
                    'Subtotal cazuri',
                ];

                $this->setSubHeaderFor('segmentation_by_age');

                $this->setVerticalHeaderFor('relationship');

                break;

            case ReportType::CASES_BY_AGGRESSOR_RELATIONSHIP_GENDER_AND_AGE:
                $this->header = [
                    'Relația cu agresorul / Vârsta victimei (minor/ major)',
                    'Distribuţia cazurilor după genul victimei',
                    'Total general cazuri',
                ];

                $this->setSubHeaderFor('gender');

                $this->setVerticalHeaderFor('relationship');

                $this->setVerticalSubHeaderFor('segmentation_by_age');

                break;

            case ReportType::CASES_BY_PRIMARY_VIOLENCE_TYPE:
                $this->header = [
                    'Tipul de violență primară',
                    'Distribuția cazurilor',
                ];

                $this->setVerticalHeaderFor('violence_primary_type');

                break;

            case ReportType::CASES_BY_VIOLENCE_TYPES:
                $this->header = [
                    'Tipurile de violență',
                    'Distribuția cazurilor',
                ];

                $this->setVerticalHeaderFor('violence_types');

                break;

            case ReportType::CASES_BY_GENDER:
                $this->header = [
                    'Genul victimei',
                    'Distribuția cazurilor',
                ];

                $this->setVerticalHeaderFor('gender');

                break;

            case ReportType::CASES_BY_AGE:
                $this->header = [
                    'Vârsta victimei',
                    'Distribuția cazurilor',
                ];

                $this->setVerticalHeaderFor('age_group');

                break;

            case ReportType::CASES_BY_LEGAL_ADDRESS:
                $this->header = [
                    'Domiciliul legal',
                    'Distribuția cazurilor',
                    'Subtotal cazuri',
                ];

                $this->setVerticalHeaderFor('legal_residence_environment');

                break;

            case ReportType::CASES_BY_EFFECTIVE_ADDRESS:
                $this->header = [
                    'Domiciliul efectiv',
                    'Distribuția cazurilor',
                ];

                $this->setVerticalHeaderFor('effective_residence_environment');

                break;

            case ReportType::CASES_BY_PRIMARY_VIOLENCE_TYPE_AND_AGE:
                $this->header = [
                    'Tipul de violență primară',
                    'Distribuţia cazurilor după vârsta victimei (minor/major)',
                    'Subtotal cazuri',
                ];

                $this->setSubHeaderFor('segmentation_by_age');

                $this->setVerticalHeaderFor('violence_primary_type');

                break;

//            case ReportType::CASES_BY_VIOLENCE_TYPES_AND_AGE:
//                $this->header = [
//                    'Nivel de studii',
//                    'Distribuția cazurilor',
//                    'Subtotal cazuri',
//                ];
//
//                                $this->setSubHeaderFor('gender');
//
//                $this->verticalHeader = Studies::options();
//                $this->verticalHeader[null] = 'Date lipsa';
//                $this->verticalHeaderKey = 'studies';
//
//                break;

                //31
            case ReportType::CASES_BY_VIOLENCE_FREQUENCY:
                $this->header = [
                    'Frecvența agresiunii',
                    'Distribuția cazurilor',
                ];

                $this->setVerticalHeaderFor('frequency_violence');

                break;

            case ReportType::CASES_BY_PRIMARY_VIOLENCE_FREQUENCY_AND_AGE:
                $this->header = [
                    'Nivel de studii',
                    'Distribuția cazurilor',
                    'Subtotal cazuri',
                ];

                $this->setSubHeaderFor('frequency_violence');

                $this->setVerticalHeaderFor('violence_primary_type');

                $this->setVerticalSubHeaderFor('segmentation_by_age');

                break;

//            case ReportType::CASES_BY_VIOLENCE_FREQUENCY_GENDER_AND_AGE:
//                $this->header = [
//                    'Nivel de studii',
//                    'Distribuția cazurilor',
//                    'Subtotal cazuri',
//                ];
//
//                                $this->setSubHeaderFor('gender');
//
//                $this->verticalHeader = Studies::options();
//                $this->verticalHeader[null] = 'Date lipsa';
//                $this->verticalHeaderKey = 'studies';
//
//                break;

//            case ReportType::CASES_BY_VIOLENCE_TYPE_AND_SERVICES_TYPES:
//                $this->header = [
//                    'Nivel de studii',
//                    'Distribuția cazurilor',
//                    'Subtotal cazuri',
//                ];
//
//                                $this->setSubHeaderFor('gender');
//
//                $this->verticalHeader = Studies::options();
//                $this->verticalHeader[null] = 'Date lipsa';
//                $this->verticalHeaderKey = 'studies';
//
//                break;

//            case ReportType::CASES_BY_VIOLENCE_TYPE_AND_SERVICES_TYPES_AND_AGE:
//                $this->header = [
//                    'Nivel de studii',
//                    'Distribuția cazurilor',
//                    'Subtotal cazuri',
//                ];
//
//                                $this->setSubHeaderFor('gender');
//
//                $this->verticalHeader = Studies::options();
//                $this->verticalHeader[null] = 'Date lipsa';
//                $this->verticalHeaderKey = 'studies';
//
//                break;

            case ReportType::CASES_BY_PRESENTATION_MODE:
                $this->header = [
                    'Modalitatea de prezentare a victimei',
                    'Distribuția cazurilor',
                ];

                $this->setVerticalHeaderFor('presentation_mode');

                break;

            case ReportType::CASES_BY_REFERRING_INSTITUTION:
                $this->header = [
                    'Instituția care trimite victima',
                    'Distribuția cazurilor',
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
                $this->subHeader['unknown'] = __('report.missing_values');

                return;
            }

            $this->subHeader[null] = __('report.missing_values');
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
        }

        $this->verticalHeaderKey = $field;

        if ($this->showMissingValues) {
            if ($this->verticalHeaderKey == 'age_group') {
                $this->verticalHeader['unknown'] = __('report.missing_values');

                return;
            }

            $this->verticalHeader[null] = __('report.missing_values');
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
                $this->verticalSubHeader['unknown'] = __('report.missing_values');

                return;
            }

            $this->verticalSubHeader[null] = __('report.missing_values');
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
            case ReportType::CASES_BY_AGE:
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

            case ReportType::CASES_BY_CIVIL_STATUS_AND_AGE:
                $this->query->selectRaw("CASE
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
            case ReportType::CASES_BY_STUDIES_AND_AGE:
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

    private function addConditions()
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

        // todo add date conditions
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
}
