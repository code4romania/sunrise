<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\ServiceResource\Pages;

use App\Actions\BackAction;
use App\Enums\CounselingSheet;
use App\Filament\Organizations\Resources\InterventionServiceResource\Pages\EditCounselingSheet;
use App\Filament\Organizations\Resources\ServiceResource;
use App\Infolists\Components\Notice;
use Filament\Actions\EditAction;
use Filament\Actions\StaticAction;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewService extends ViewRecord
{
    protected static string $resource = ServiceResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->serviceWithoutStatusCondition->name;
    }

    public function getBreadcrumbs(): array
    {
        return [
            self::getResource()::getUrl() => __('service.headings.navigation'),
            self::getResource()::getUrl('view', ['record' => $this->getRecord()]) => $this->getTitle(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(ServiceResource::getUrl()),

            EditAction::make()
                ->label(__('service.actions.change_service')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()
                ->visible(fn () => $this->getRecord()->serviceWithoutStatusCondition->counseling_sheet)
                ->maxWidth('3xl')
                ->schema([
                    Notice::make('counseling_sheet')
                        ->state(__('service.helper_texts.counseling_sheet'))
                        ->icon('heroicon-o-document-text')
                        ->action(
                            Action::make('view_counseling_sheet')
                                ->label(__('service.actions.view_counseling_sheet'))
                                ->modalHeading(
                                    $this->getRecord()
                                        ->serviceWithoutStatusCondition
                                        ->counseling_sheet
                                        ?->getLabel()
                                )
                                ->form(function () {
                                    $counselingSheet = $this->getRecord()->serviceWithoutStatusCondition->counseling_sheet;

                                    if (CounselingSheet::isValue($counselingSheet, CounselingSheet::LEGAL_ASSISTANCE)) {
                                        return EditCounselingSheet::getLegalAssistanceForm();
                                    }

                                    if (CounselingSheet::isValue($counselingSheet, CounselingSheet::PSYCHOLOGICAL_ASSISTANCE)) {
                                        return EditCounselingSheet::getSchemaForPsychologicalAssistance();
                                    }

                                    if (CounselingSheet::isValue($counselingSheet, CounselingSheet::SOCIAL_ASSISTANCE)) {
                                        return EditCounselingSheet::getSchemaForSocialAssistance();
                                    }

                                    return [];
                                })
                                ->disabledForm()
                                ->modalSubmitAction(fn (StaticAction $action) => $action->hidden())
                                ->link()
                                ->modalAutofocus(false),
                        ),
                ]),
        ]);
    }

    protected function hasInfolist(): bool
    {
        return true;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ServiceResource\Widgets\ListServiceInterventions::class,
        ];
    }
}
