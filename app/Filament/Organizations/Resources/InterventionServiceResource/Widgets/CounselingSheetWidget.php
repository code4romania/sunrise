<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionServiceResource\Widgets;

use App\Enums\CounselingSheet;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Infolists\Components\SectionHeader;
use App\Models\InterventionService;
use App\Widgets\InfolistWidget;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class CounselingSheetWidget extends InfolistWidget
{
    public ?InterventionService $record = null;

    protected function getInfoListSchema(): array
    {
        $counselingSheet = $this->record->organizationService->service->counseling_sheet;
        if (blank($counselingSheet)) {
            return [];
        }

        $schema = [];
        if (CounselingSheet::isValue($counselingSheet, CounselingSheet::LEGAL_ASSISTANCE)) {
            $schema = $this->getSchemaForLegalAssistance();
        }

        if (CounselingSheet::isValue($counselingSheet, CounselingSheet::PSYCHOLOGICAL_ASSISTANCE)) {
            $schema = $this->getSchemaForPsychologicalAssistance();
        }

        return [
            Section::make()
                ->relationship('counselingSheet')
                ->maxWidth('3xl')
                ->schema([
                    SectionHeader::make('counseling_sheet')
                        ->state(__('intervention_plan.headings.counseling_sheet'))
                        ->action(
                            Action::make('view')
                                ->label(__('general.action.edit'))
                                ->icon('heroicon-o-pencil')
                                ->url(InterventionPlanResource::getUrl(
                                    'edit_counseling_sheet',
                                    [
                                        'parent' => $this->record->interventionPlan,
                                        'record' => $this->record,
                                    ]
                                ))
                                ->link(),
                        ),
                    ...$schema]),
        ];
    }

    private function getSchemaForLegalAssistance(): array
    {
        return [
            Section::make(__('intervention_plan.headings.patrimony_data'))
                ->columns()
                ->schema([
                    TextEntry::make('data.patrimony')
                        ->label(__('intervention_plan.labels.patrimony')),
                    TextEntry::make('data.possession_mode')
                        ->label(__('intervention_plan.labels.possession_mode')),
                    TextEntry::make('data.possession_observation')
                        ->label(__('intervention_plan.labels.possession_observation')),
                ]),

            Section::make(__('intervention_plan.headings.file_documents'))
                ->columns()
                ->schema([
                    Group::make()
                        ->schema([
                            TextEntry::make('data.original_documents')
                                ->label(__('intervention_plan.labels.original_documents')),
                            TextEntry::make('data.original_documents_observation')
                                ->label(__('intervention_plan.labels.original_documents_observation')),
                        ]),

                    Group::make()
                        ->schema([
                            TextEntry::make('data.copy_documents')
                                ->label(__('intervention_plan.labels.copy_documents')),
                            TextEntry::make('data.copy_documents_observation')
                                ->label(__('intervention_plan.labels.copy_documents_observation')),
                        ]),
                ]),

            Section::make(__('intervention_plan.headings.institution_contacted'))
                ->schema([
                    RepeatableEntry::make('data.institutions')
                        ->hiddenLabel()
                        ->columns()
                        ->schema([
                            TextEntry::make('institution')
                                ->label(__('intervention_plan.labels.responsible_institution')),
                            TextEntry::make('contact_date')
                                ->label(__('intervention_plan.labels.contact_date')),
                            TextEntry::make('phone')
                                ->label(__('intervention_plan.labels.phone')),
                            TextEntry::make('contact_person')
                                ->label(__('intervention_plan.labels.contact_person')),
                        ]),

                ]),

            Section::make(__('intervention_plan.headings.final_observations'))
                ->schema([
                    TextEntry::make('data.observations')
                        ->label(__('intervention_plan.labels.final_observation'))
                        ->columnSpanFull(),
                ]),

        ];
    }

    private function getSchemaForPsychologicalAssistance(): array
    {
        return [
            Section::make(__('intervention_plan.headings.medical_details'))
                ->columns()
                ->schema([
                    TextEntry::make('data.substance_use')
                        ->label(__('intervention_plan.labels.substance_use')),
                    TextEntry::make('data.substance_types')
                        ->label(__('intervention_plan.labels.substance_types')),
                    TextEntry::make('data.observations_substances')
                        ->label(__('intervention_plan.labels.observations_substances'))
                        ->columnSpanFull(),
                    TextEntry::make('data.current_contraception')
                        ->label(__('intervention_plan.labels.current_contraception')),
                    TextEntry::make('data.observations_contraception')
                        ->label(__('intervention_plan.labels.observations_contraception')),
                    TextEntry::make('data.psychiatric_history')
                        ->label(__('intervention_plan.labels.psychiatric_history')),
                    TextEntry::make('data.psychiatric_history_observations')
                        ->label(__('intervention_plan.labels.psychiatric_history_observations')),
                    TextEntry::make('data.investigations_for_psychiatric_pathology')
                        ->label(__('intervention_plan.labels.investigations_for_psychiatric_pathology')),
                    TextEntry::make('data.investigations_observations')
                        ->label(__('intervention_plan.labels.investigations_observations')),
                    TextEntry::make('data.treatment_for_psychiatric_pathology')
                        ->label(__('intervention_plan.labels.treatment_for_psychiatric_pathology')),
                    TextEntry::make('data.treatment_observations')
                        ->label(__('intervention_plan.labels.treatment_observations')),
                ]),

            Section::make(__('intervention_plan.headings.history'))
                ->schema([
                    TextEntry::make('data.relationship_history')
                        ->label(__('intervention_plan.labels.relationship_history')),
                    TextEntry::make('data.last_incident_description')
                        ->label(__('intervention_plan.labels.last_incident_description')),
                ]),

            Section::make(__('intervention_plan.headings.violence_forms'))
                ->schema([
                    TextEntry::make('data.violence_history_forms')
                        ->label(__('intervention_plan.labels.violence_history_forms')),
                    TextEntry::make('data.physics')
                        ->label(__('intervention_plan.labels.physics')),
                    TextEntry::make('data.sexed')
                        ->label(__('intervention_plan.labels.sexed')),
                    TextEntry::make('data.psychological')
                        ->label(__('intervention_plan.labels.psychological')),
                    TextEntry::make('data.verbal')
                        ->label(__('intervention_plan.labels.verbal')),
                    TextEntry::make('data.sociable')
                        ->label(__('intervention_plan.labels.sociable')),
                    TextEntry::make('data.economic')
                        ->label(__('intervention_plan.labels.economic')),
                    TextEntry::make('data.cybernetics')
                        ->label(__('intervention_plan.labels.cybernetics')),
                    TextEntry::make('data.spiritual')
                        ->label(__('intervention_plan.labels.spiritual')),
                    TextEntry::make('data.physical_violence_description')
                        ->label(__('intervention_plan.labels.physical_violence_description')),
                    TextEntry::make('data.sexual_violence_description')
                        ->label(__('intervention_plan.labels.sexual_violence_description')),
                    TextEntry::make('data.psychological_violence_description')
                        ->label(__('intervention_plan.labels.psychological_violence_description')),
                    TextEntry::make('data.verbal_violence_description')
                        ->label(__('intervention_plan.labels.verbal_violence_description')),
                    TextEntry::make('data.social_violence_description')
                        ->label(__('intervention_plan.labels.social_violence_description')),
                    TextEntry::make('data.economic_violence_description')
                        ->label(__('intervention_plan.labels.economic_violence_description')),
                    TextEntry::make('data.cyber_violence_description')
                        ->label(__('intervention_plan.labels.cyber_violence_description')),
                    TextEntry::make('data.spiritual_violence_description')
                        ->label(__('intervention_plan.labels.spiritual_violence_description')),
                ]),

            Section::make(__('intervention_plan.headings.violence_effects'))
                ->schema([
                    TextEntry::make('data.physical_effects')
                        ->label(__('intervention_plan.labels.physical_effects')),
                    TextEntry::make('data.psychological_effects')
                        ->label(__('intervention_plan.labels.psychological_effects')),
                    TextEntry::make('data.social_effects')
                        ->label(__('intervention_plan.labels.social_effects')),
                ]),

            Section::make(__('intervention_plan.headings.risk_factors'))
                ->schema([
                    TextEntry::make('data.risk_factors_description')
                        ->label(__('intervention_plan.labels.risk_factors_description')),
                ]),

            Section::make(__('intervention_plan.headings.protection_factors'))
                ->schema([
                    TextEntry::make('data.internal_resources')
                        ->label(__('intervention_plan.labels.internal_resources')),
                    TextEntry::make('data.external_resources')
                        ->label(__('intervention_plan.labels.external_resources')),
                ]),

            Section::make(__('intervention_plan.headings.request'))
                ->schema([
                    TextEntry::make('data.requests_description')
                        ->label(__('intervention_plan.labels.requests_description')),
                ]),

            Section::make(__('intervention_plan.headings.psychological_evaluation'))
                ->schema([
                    TextEntry::make('data.first_meeting_psychological_evaluation')
                        ->label(__('intervention_plan.labels.first_meeting_psychological_evaluation')),
                ]),
        ];
    }

    public function getDisplayName(): string
    {
        return __('intervention_plan.headings.counseling_sheet');
    }
}
