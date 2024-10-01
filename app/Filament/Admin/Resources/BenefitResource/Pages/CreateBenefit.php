<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BenefitResource\Pages;

use App\Filament\Admin\Pages\NomenclatureList;
use App\Filament\Admin\Resources\BenefitResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateBenefit extends CreateRecord
{
    protected static string $resource = BenefitResource::class;

    protected static bool $canCreateAnother = false;

    public function getBreadcrumbs(): array
    {
        return [
            NomenclatureList::getUrl() => __('nomenclature.titles.list'),
            self::$resource::getUrl('create') => __('nomenclature.actions.add_benefit'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.actions.add_benefit');
    }

    protected function getRedirectUrl(): string
    {
        return self::$resource::getUrl('view', ['record' => $this->getRecord()]);
    }
}
