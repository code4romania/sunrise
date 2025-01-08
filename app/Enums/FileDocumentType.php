<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum FileDocumentType: string implements HasLabel
{
    use Enums\HasLabel;
    use Enums\Arrayable;
    use Enums\Comparable;

    case MARRIAGE_CERTIFICATE = 'marriage_certificate';
    case CHILDREN_BIRTH_CERTIFICATE = 'children_birth_certificate';
    case LAND_DEED_EXTRACT = 'land_deed_extract';
    case RENTAL_AGREEMENT = 'rental_agreement';
    case SALE_PURCHASE_AGREEMENT = 'sale_purchase_agreement';
    case IML_CERTIFICATE = 'iml_certificate';

    case COURT_SENTENCES = 'court_sentences';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.file_document_type';
    }
}
