<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceResource\Pages;

use App\Filament\Admin\Pages\NomenclatureList;
use App\Filament\Admin\Resources\ServiceResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewService extends ViewRecord
{
    protected static string $resource = ServiceResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            NomenclatureList::getUrl() => __('nomenclature.titles.list'),
            ServiceResource::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->name,
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->name;
    }

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(__('nomenclature.actions.edit_service')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()
                ->maxWidth('3xl')
                ->schema([
                    TextEntry::make('name')
                        ->label(__('nomenclature.labels.service_name')),
                    TextEntry::make('counseling_sheet')
                        ->label(__('nomenclature.labels.counseling_sheet')),

                ]),
        ]);
    }

    protected function getFooterWidgets(): array
    {
        return [
            ServiceResource\Widgets\ServiceInterventionsWidget::class,
        ];
    }
}
