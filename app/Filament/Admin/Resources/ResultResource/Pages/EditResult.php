<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ResultResource\Pages;

use App\Filament\Admin\Actions\ChangeNomenclatureStatusAction;
use App\Filament\Admin\Resources\ResultResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditResult extends EditRecord
{
    protected static string $resource = ResultResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            self::$resource::getUrl() => __('nomenclature.titles.list'),
            self::$resource::getUrl('edit', ['record' => $this->getRecord()]) => __('nomenclature.headings.edit_result_title', ['name' => $this->getRecord()->name]),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.headings.edit_result_title', ['name' => $this->getRecord()->name]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ChangeNomenclatureStatusAction::make(),

            // TODO: disable button if is used by ngo
            Actions\DeleteAction::make()
                ->label(__('nomenclature.actions.delete_result'))
                ->modalHeading(__('nomenclature.headings.delete_result_modal_heading'))
                ->outlined()
                ->modalSubmitActionLabel(__('nomenclature.actions.delete_result')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return self::$resource::getUrl();
    }
}
