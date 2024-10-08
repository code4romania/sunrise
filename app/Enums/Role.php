<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel as HasLabelTrait;
use Filament\Support\Contracts\HasLabel;

/** @deprecated  use App\Models\Role */
enum Role: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use HasLabelTrait;

    case COORDINATOR = 'coordinator';
    case MANGER = 'manger';
    case CHEF_MANAGER = 'chef_manager';
    case CHEF_SERVICE = 'chef_service';
    case PSYCHOLOGICAL_ADVICE = 'psychological_advice';
    case PSYCHOTHERAPIST = 'psychotherapist';
    case CLINICAL_PSYCHOLOGIST = 'clinical_psychologist';
    case PSYCHO_PEDAGOGUE = 'psycho_pedagogue';
    case SOCIAL_WORKER = 'social_worker';
    case LEGAL_ADVISOR = 'legal_advisor';
    case FACILITATOR = 'facilitator';
    case TRAINER = 'trainer';
    case DOCTOR = 'doctor';
    case MEDICAL_ASSISTANT = 'medical_assistant';
    case OCCUPATIONAL_THERAPIST = 'occupational_therapist';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.role';
    }
}
