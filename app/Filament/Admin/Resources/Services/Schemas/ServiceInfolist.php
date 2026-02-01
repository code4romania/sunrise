<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Services\Schemas;

use App\Enums\CounselingSheet;
use App\Filament\Admin\Resources\Services\ServiceResource;
use App\Infolists\Components\Notice;
use App\Models\Service;
use App\Schemas\CounselingSheetFormSchemas;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('nomenclature.headings.service'))
                    ->columnSpanFull()
                    ->headerActions([
                        EditAction::make()
                            ->url(fn (Service $record) => ServiceResource::getUrl('edit', ['record' => $record])),
                    ])
                    ->schema([
                        Grid::make(2)
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('nomenclature.labels.name')),
                                TextEntry::make('counseling_sheet')
                                    ->label(__('nomenclature.labels.counseling_sheet'))
                                    ->badge(),
                                TextEntry::make('status')
                                    ->label(__('nomenclature.labels.status'))
                                    ->badge(),
                            ]),
                    ]),

                Section::make()
                    ->visible(fn (Service $record) => $record->counseling_sheet !== null)
                    ->columnSpanFull()
                    ->schema([
                        Notice::make('counseling_sheet')
                            ->state(__('service.helper_texts.counseling_sheet'))
                            ->icon('heroicon-o-document-text')
                            ->action(
                                Action::make('view_counseling_sheet')
                                    ->label(__('service.actions.view_counseling_sheet'))
                                    ->modalHeading(fn (Service $record) => $record->counseling_sheet?->getLabel())
                                    ->schema(fn (Service $record) => match ($record->counseling_sheet) {
                                        CounselingSheet::LEGAL_ASSISTANCE => CounselingSheetFormSchemas::getLegalAssistanceForm(),
                                        CounselingSheet::PSYCHOLOGICAL_ASSISTANCE => CounselingSheetFormSchemas::getSchemaForPsychologicalAssistance(),
                                        CounselingSheet::SOCIAL_ASSISTANCE => CounselingSheetFormSchemas::getSchemaForSocialAssistance(null),
                                        default => [],
                                    })
                                    ->disabledForm()
                                    ->modalSubmitAction(false)
                                    ->modalCancelActionLabel(__('filament-actions::view.single.modal.actions.close.label'))
                                    ->link(),
                            ),
                    ]),

                Section::make(__('nomenclature.headings.service_intervention'))
                    ->schema([
                        RepeatableEntry::make('serviceInterventions')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make(__('nomenclature.labels.nr')),
                                TableColumn::make(__('nomenclature.labels.intervention_name')),
                                TableColumn::make(__('nomenclature.labels.institutions')),
                                TableColumn::make(__('nomenclature.labels.centers')),
                                TableColumn::make(__('nomenclature.labels.status')),
                            ])
                            ->schema([
                                TextEntry::make('sort'),
                                TextEntry::make('name'),
                                TextEntry::make('institutions_count'),
                                TextEntry::make('organizations_count'),
                                TextEntry::make('status')
                                    ->badge(),
                            ]),
                    ]),
            ]);
    }
}
