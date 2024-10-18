<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BenefitResource\Pages;

use App\Filament\Admin\Resources\BenefitResource;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListBenefits extends ManageRecords
{
    protected static string $resource = BenefitResource::class;

    protected static string $view = 'filament.admin.pages.nomenclature-list';

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.headings.benefits');
    }
}
