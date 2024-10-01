<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BenefitResource\Pages;

use App\Enums\GeneralStatus;
use App\Filament\Admin\Pages\NomenclatureList;
use App\Filament\Admin\Resources\BenefitResource;
use App\Filament\Admin\Resources\BenefitResource\Actions\ChangeStatusAction;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditBenefit extends EditRecord
{
    protected static string $resource = BenefitResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            NomenclatureList::getUrl() => __('nomenclature.titles.list'),
            self::$resource::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->name,
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->name;
    }

    protected function getRedirectUrl(): string
    {
        return self::$resource::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ChangeStatusAction::make(),

            // TODO make disable if is use
            Actions\DeleteAction::make()
                ->label(__('nomenclature.actions.delete_benefit'))
                ->icon('heroicon-s-trash')
                ->outlined()
                ->disabled(fn () => GeneralStatus::isValue($this->getRecord()->status, GeneralStatus::ACTIVE))
                ->successRedirectUrl(NomenclatureList::getUrl())
                // tooltip doesn't work if action is disabled
                ->tooltip(fn (Actions\DeleteAction $action) => $action->isDisabled() ? __('nomenclature.helper_texts.delete_benefit') : ''),
        ];
    }
}
