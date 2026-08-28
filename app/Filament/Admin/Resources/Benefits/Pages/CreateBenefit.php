<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Benefits\Pages;

use App\Filament\Admin\Resources\Benefits\BenefitResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateBenefit extends CreateRecord
{
    protected static string $resource = BenefitResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.titles.list').' - '.__('nomenclature.headings.benefits').': '.__('nomenclature.actions.add_benefit');
    }
}
