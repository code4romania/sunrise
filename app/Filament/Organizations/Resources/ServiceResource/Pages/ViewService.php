<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\ServiceResource\Pages;

use App\Actions\BackAction;
use App\Enums\CounselingSheet;
use App\Filament\Organizations\Resources\InterventionServiceResource\Pages\EditCounselingSheet;
use App\Filament\Organizations\Resources\ServiceResource;
use App\Infolists\Components\Notice;
use App\Infolists\Components\TableEntry;
use App\Models\BeneficiaryIntervention;
use Filament\Actions\EditAction;
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

    public function getTitle(): string|Htmlable
    {
        return __('service.headings.view_service_page', ['service_name' => strtolower($this->getRecord()->serviceWithoutStatusCondition->name)]);
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
        $this->getRecord()
            ->load([
                'interventions.serviceInterventionWithoutStatusCondition',
                'interventions.beneficiaryInterventions.interventionPlan',
            ]);

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

            TableEntry::make('interventions')
                ->columnSpanFull()
                ->hiddenLabel()
                ->schema([
                    TextEntry::make('serviceInterventionWithoutStatusCondition.name')
                        ->hiddenLabel()
                        ->label(__('service.labels.interventions')),

                    TextEntry::make('cases')
                        ->hiddenLabel()
                        ->label(__('service.labels.cases'))
                        ->default(
                            fn ($record) => $record->beneficiaryInterventions
                                ?->map(fn (BeneficiaryIntervention $beneficiaryIntervention) => $beneficiaryIntervention->interventionPlan->beneficiary_id)
                                ->unique()
                                ->count()
                        ),

                    TextEntry::make('status')
                        ->hiddenLabel()
                        ->label(__('service.labels.status')),
                ]),

        ]);
    }
}
