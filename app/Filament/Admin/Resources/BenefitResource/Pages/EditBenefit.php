<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BenefitResource\Pages;

use App\Actions\BackAction;
use App\Filament\Admin\Actions\ChangeNomenclatureStatusAction;
use App\Filament\Admin\Resources\BenefitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditBenefit extends EditRecord
{
    protected static string $resource = BenefitResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            self::$resource::getUrl() => __('nomenclature.titles.list'),
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
            BackAction::make()
                ->url($this->getRedirectUrl()),

            ChangeNomenclatureStatusAction::make(),

            Actions\DeleteAction::make()
                ->label(__('nomenclature.actions.delete_benefit'))
                ->icon('heroicon-s-trash')
                ->outlined()
                ->disabled(fn () => $this->getRecord()->benefitServices()->count())
                ->successRedirectUrl(self::$resource::getUrl())
                // tooltip doesn't work if action is disabled
                ->tooltip(fn (Actions\DeleteAction $action) => $action->isDisabled() ? __('nomenclature.helper_texts.delete_benefit') : ''),
        ];
    }
}
