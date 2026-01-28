<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum ReportType: string implements HasLabel
{
    use Enums\HasLabel;
    use Arrayable;
    use Comparable;

    case CASES_BY_AGE = 'cases_by_age';
    case CASES_BY_AGE_SEGMENTATION = 'cases_by_age_segmentation';
    case CASES_BY_GENDER = 'cases_by_gender';
    case CASES_BY_CITIZENSHIP = 'cases_by_citizenship';
    case CASES_BY_ETHNICITY = 'cases_by_ethnicity';
    case CASES_BY_CIVIL_STATUS = 'cases_by_civil_status';
    case CASES_BY_CIVIL_STATUS_AND_GENDER = 'cases_by_civil_status_and_gender';
    case CASES_BY_CIVIL_STATUS_AND_AGE = 'cases_by_civil_status_and_age';
    case CASES_BY_STUDIES = 'cases_by_studies';
    case CASES_BY_STUDIES_AND_GENDER = 'cases_by_studies_and_gender';
    case CASES_BY_STUDIES_AND_EFFECTIVE_ADDRESS = 'cases_by_studies_and_effective_address';
    case CASES_BY_STUDIES_AND_AGE = 'cases_by_studies_and_age';
    case CASES_BY_LEGAL_ADDRESS = 'cases_by_legal_address';
    case CASES_BY_EFFECTIVE_ADDRESS = 'cases_by_effective_address';
    case CASES_BY_OCCUPATION = 'cases_by_occupation';
    case CASES_BY_OCCUPATION_AND_EFFECTIVE_ADDRESS = 'cases_by_occupation_and_effective_address';
    case CASES_BY_OCCUPATION_EFFECTIVE_ADDRESS_AND_GENDER = 'cases_by_occupation_and_effective_address_and_gender';
    case CASES_BY_AGE_GENDER_AND_LEGAL_ADDRESS = 'cases_by_age_gender_and_legal_address';
    case CASES_BY_AGE_GENDER_AND_EFFECTIVE_ADDRESS = 'cases_by_age_gender_and_effective_address';
    case CASES_BY_HOME_OWNERSHIP = 'cases_by_home_ownership';
    case CASES_BY_HOME_OWNERSHIP_AND_EFFECTIVE_ADDRESS = 'cases_by_home_ownership_and_effective_address';
    case CASES_BY_HOME_OWNERSHIP_EFFECTIVE_ADDRESS_AND_GENDER = 'cases_by_home_ownership_effective_address_and_gender';
    case CASES_BY_INCOME = 'cases_by_income';
    case CASES_BY_INCOME_AND_EFFECTIVE_ADDRESS = 'cases_by_income_and_effective_address';
    case CASES_BY_INCOME_EFFECTIVE_ADDRESS_AND_GENDER = 'cases_by_income_effective_address_and_gender';
    case CASES_BY_AGGRESSOR_RELATIONSHIP = 'cases_by_aggressor_relationship';
    case CASES_BY_AGGRESSOR_RELATIONSHIP_AND_AGE = 'cases_by_aggressor_relationship_and_age';
    case CASES_BY_AGGRESSOR_RELATIONSHIP_GENDER_AND_AGE = 'cases_by_aggressor_relationship_and_age_and_gender';
    case CASES_BY_PRIMARY_VIOLENCE_TYPE = 'cases_by_primary_violence_type';
    case CASES_BY_VIOLENCE_TYPES = 'cases_by_violence_types';
    case CASES_BY_VIOLENCE_FREQUENCY = 'cases_by_violence_frequency';
    case CASES_BY_PRIMARY_VIOLENCE_TYPE_AND_AGE = 'cases_by_primary_violence_type_and_age';
    case CASES_BY_PRIMARY_VIOLENCE_FREQUENCY_AND_AGE = 'cases_by_primary_violence_frequency_and_age';
    case CASES_BY_PRESENTATION_MODE = 'cases_by_presentation_mode';
    case CASES_BY_REFERRING_INSTITUTION = 'cases_by_referring_institution';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.report_type';
    }
}
