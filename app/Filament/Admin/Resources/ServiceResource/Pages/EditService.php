<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceResource\Pages;

use App\Enums\GeneralStatus;
use App\Filament\Admin\Pages\NomenclatureList;
use App\Filament\Admin\Resources\ServiceResource;
use App\Filament\Admin\Resources\ServiceResource\Actions\ChangeStatusAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

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

    public function getTitle(): string|Htmlable
    {
        return  $this->getRecord()->name;
    }

    protected function getActions(): array
    {
        return [
            ChangeStatusAction::make(),

            //TODO disable if is used
            DeleteAction::make()
                ->disabled(fn () => GeneralStatus::isValue($this->getRecord()->status, GeneralStatus::ACTIVE))
                ->successRedirectUrl(NomenclatureList::getUrl()),
        ];
    }
}
