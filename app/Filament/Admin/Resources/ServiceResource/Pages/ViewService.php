<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceResource\Pages;

use App\Actions\BackAction;
use App\Enums\CounselingSheet;
use App\Filament\Admin\Resources\ServiceResource;
use App\Filament\Organizations\Resources\InterventionServiceResource\Pages\EditCounselingSheet;
use App\Infolists\Components\Notice;
use App\Infolists\Components\TableEntry;
use App\Models\Service;
use Filament\Actions;
use Filament\Actions\StaticAction;
use Filament\Infolists\Components\Actions\Action;
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
            self::$resource::getUrl() => __('nomenclature.titles.list'),
            self::$resource::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->name,
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->name;
    }

    protected function getActions(): array
    {
        return [
            BackAction::make()
                ->url(ServiceResource::getUrl()),

            Actions\EditAction::make()
                ->label(__('nomenclature.actions.edit_service')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()
                ->visible(fn (Service $record) => $record->counseling_sheet)
                ->maxWidth('3xl')
                ->schema([
                    Notice::make('counseling_sheet')
                        ->state(__('service.helper_texts.counseling_sheet'))
                        ->icon('heroicon-o-document-text')
                        ->action(
                            Action::make('view_counseling_sheet')
                                ->label(__('service.actions.view_counseling_sheet'))
                                ->modalHeading(fn (Service $record) => $record->counseling_sheet?->getLabel())
                                ->form(fn (Service $record) => match ($record->counseling_sheet) {
                                    CounselingSheet::LEGAL_ASSISTANCE => EditCounselingSheet::getLegalAssistanceForm(),
                                    CounselingSheet::PSYCHOLOGICAL_ASSISTANCE => EditCounselingSheet::getSchemaForPsychologicalAssistance(),
                                    CounselingSheet::SOCIAL_ASSISTANCE => EditCounselingSheet::getSchemaForSocialAssistance(),
                                    default => [],
                                })
                                ->disabledForm()
                                ->modalAutofocus(false)
                                ->modalSubmitAction(fn (StaticAction $action) => $action->hidden())
                                ->link(),
                        ),
                ]),

            Section::make()
                ->schema([
                    TableEntry::make('serviceInterventions')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('name')
                                ->label(__('nomenclature.labels.intervention_name'))
                                ->hiddenLabel(),

                            TextEntry::make('institutions_count')
                                ->label(__('nomenclature.labels.institutions'))
                                ->hiddenLabel(),

                            TextEntry::make('organizations_count')
                                ->label(__('nomenclature.labels.centers'))
                                ->hiddenLabel(),

                            TextEntry::make('status')
                                ->label(__('nomenclature.labels.status'))
                                ->hiddenLabel(),
                        ]),
                ]),
        ]);
    }
}
