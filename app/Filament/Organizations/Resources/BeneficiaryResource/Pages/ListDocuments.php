<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ListDocuments extends ViewRecord
{
    protected static string $resource = BeneficiaryResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            BeneficiaryResource\Widgets\ViewDocuments::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 1;
    }

    /**
     * @return string|Htmlable
     */
    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.documents.title.page');
    }
}
