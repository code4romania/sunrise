<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Benefits\Pages;

use App\Filament\Admin\Resources\Benefits\BenefitResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListBenefits extends ListRecords
{
    protected static string $resource = BenefitResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.titles.list').' - '.__('nomenclature.headings.benefits');
    }
}
