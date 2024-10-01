<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BenefitResource\Pages;

use App\Filament\Admin\Pages\NomenclatureList;
use App\Filament\Admin\Resources\BenefitResource;
use App\Filament\Admin\Resources\BenefitResource\Widgets\BenefitTypesWidget;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewBenefit extends ViewRecord
{
    protected static string $resource = BenefitResource::class;

    public function getFooterWidgetsColumns(): int|string|array
    {
        return 1;
    }

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(__('nomenclature.actions.edit_benefit')),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            NomenclatureList::getUrl() => __('nomenclature.titles.list'),
            self::$resource::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->name,
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    protected function getFooterWidgets(): array
    {
        return [
            BenefitTypesWidget::class,
        ];
    }
}
