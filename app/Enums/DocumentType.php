<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel as HasLabelTrait;
use Filament\Support\Contracts\HasLabel;

enum DocumentType: string implements HasLabel
{
    use Arrayable;
    use Comparable;
    use HasLabelTrait;

    case CONTRACT = 'contract';
    case FORM = 'form';
    case FILE = 'file';
    case REQUEST = 'request';
    case ACCORD = 'accord';
    case DECISION = 'decision';
    case CERTIFICATE = 'certificate';
    case DECLARATION = 'declaration';
    case NOTIFICATION = 'notification';
    case REPORT = 'report';
    case VERBAL_PROCESS = 'verbal_process';
    case ID_CARD = 'id_card';
    case ID_CARD_CHILD = 'id_card_child';
    case CIVIL_STATUS = 'civil_status';
    case STUDIES_DOCUMENT = 'studies_document';
    case PROPRIETY_DOCUMENT = 'propriety_document';
    case MEDICAL_DOCUMENT = 'medical_document';
    case MEDICO_LEGAL_CERTIFICATE = 'medico_legal_certificate';
    case PROTECTION_ORDER = 'protection_order';
    case LEGAL_DOCUMENT = 'legal_document';
    case EVALUATION_QUESTIONNAIRE = 'evaluation_questionnaire';
    case CHILD_BORN_CERTIFICATE = 'child_born_certificate';
    case STANDARD_SHEETS = 'standard_sheets';
    case DOCUMENT = 'document';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.document_type';
    }
}
