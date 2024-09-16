<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\RoleResource\Pages;

use App\Enums\GeneralStatus;
use App\Filament\Admin\Actions\ChangeNomenclatureStatusAction;
use App\Filament\Admin\Pages\NomenclatureList;
use App\Filament\Admin\Resources\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->name;
    }

    public function getBreadcrumbs(): array
    {
        return [
            NomenclatureList::getUrl() => __('nomenclature.titles.list'),
            RoleResource::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->name,
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return self::$resource::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ChangeNomenclatureStatusAction::make(),

            //TODO disable if is used
            DeleteAction::make()
                ->label(__('nomenclature.actions.delete_role'))
                ->outlined()
                ->icon('heroicon-o-trash')
                ->disabled(fn () => GeneralStatus::isValue($this->getRecord()->status, GeneralStatus::ACTIVE))
                ->successRedirectUrl(NomenclatureList::getUrl()),
        ];
    }
}
