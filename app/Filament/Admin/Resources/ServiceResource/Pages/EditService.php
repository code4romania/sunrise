<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceResource\Pages;

use App\Filament\Admin\Pages\NomenclatureList;
use App\Filament\Admin\Resources\ServiceResource;
use App\Filament\Admin\Resources\ServiceResource\Actions\ChangeStatusAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            NomenclatureList::getUrl() => __('nomenclature.titles.list'),
            ServiceResource::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->name,
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return self::$resource::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getActions(): array
    {
        return [
            ChangeStatusAction::make(),
            DeleteAction::make(),
        ];
    }
}
