<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceResource\Pages;

use App\Actions\BackAction;
use App\Enums\CounselingSheet;
use App\Filament\Admin\Resources\ServiceResource;
use App\Filament\Admin\Resources\ServiceResource\Widgets\InterventionsWidget;
use App\Filament\Organizations\Resources\InterventionServiceResource\Pages\EditCounselingSheet;
use App\Infolists\Components\Notice;
use Filament\Actions;
use Filament\Actions\StaticAction;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
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
                ->visible(fn () => $this->getRecord()->counseling_sheet)
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
                                        ->counseling_sheet
                                        ?->getLabel()
                                )
                                ->form(function () {
                                    $counselingSheet = $this->getRecord()->counseling_sheet;

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
                                ->modalAutofocus(false)
                                ->modalSubmitAction(fn (StaticAction $action) => $action->hidden())
                                ->link(),
                        ),
                ]),
        ]);
    }

    protected function hasInfolist(): bool
    {
        return true;
    }

    protected function getFooterWidgets(): array
    {
        return [
            InterventionsWidget::class,
        ];
    }
}
