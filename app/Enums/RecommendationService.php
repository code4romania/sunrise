<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum RecommendationService: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use Enums\HasLabel;

    case PSYCHOLOGICAL_ADVICE = 'psychological_advice';
    case LEGAL_ADVICE = 'legal_advice';
    case LEGAL_ASSISTANCE = 'legal_assistance';
    case FAMILY_COUNSELING = 'family_counseling';
    case PRENATAL_ADVICE = 'prenatal_advice';
    case SOCIAL_ADVICE = 'social_advice';
    case MEDICAL_SERVICES = 'medical_services';
    case MEDICAL_PAYMENT = 'medical_payment';
    case SECURING_RESIDENTIAL_SPACES = 'securing_residential_spaces';
    case OCCUPATIONAL_PROGRAM_SERVICES = 'occupational_program_services';
    case EDUCATIONAL_SERVICES_FOR_CHILDREN = 'educational_services_for_children';
    case TEMPORARY_SHELTER_SERVICES = 'temporary_shelter_services';
    case PROTECTION_ORDER = 'protection_order';
    case CRISIS_ASSISTANCE = 'crisis_assistance';
    case SAFETY_PLAN = 'safety_plan';
    case OTHER_SERVICES = 'other_services';

    protected function labelKeyPrefix(): ?string
    {
        return 'beneficiary.section.detailed_evaluation.labels';
    }
}
