<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BenefitResource\Pages;

use Filament\Schemas\Schema;
use App\Actions\BackAction;
use App\Filament\Admin\Resources\BenefitResource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Actions\EditAction;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewBenefit extends ViewRecord
{
    protected static string $resource = BenefitResource::class;

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(BenefitResource::getUrl()),

            EditAction::make()
                ->label(__('nomenclature.actions.edit_benefit')),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            self::$resource::getUrl() => __('nomenclature.titles.list'),
            self::$resource::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->name,
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->maxWidth('3xl')
                ->schema([
                    RepeatableEntry::make('benefitTypes')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('name')
                                ->label(__('nomenclature.labels.benefit_type_name'))
                                ->hiddenLabel(),

                            TextEntry::make('status')
                                ->label(__('nomenclature.labels.status'))
                                ->hiddenLabel(),
                        ]),
                ]),
        ]);
    }
}
