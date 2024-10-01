<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionServiceResource\Pages;

use App\Enums\CounselingSheet;
use App\Enums\Drug;
use App\Enums\Frequency;
use App\Enums\Ternary;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Filament\Organizations\Resources\InterventionServiceResource;
use App\Forms\Components\Select;
use App\Services\Breadcrumb\InterventionPlanBreadcrumb;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;

class EditCounselingSheet extends EditRecord
{
    protected static string $resource = InterventionServiceResource::class;

    protected function getRedirectUrl(): ?string
    {
        return InterventionPlanResource::getUrl('view_intervention_service', ['parent' => $this->getRecord()->interventionPlan,
            'record' => $this->record]);
    }

    public function getBreadcrumbs(): array
    {
        return InterventionPlanBreadcrumb::make($this->getRecord()->interventionPlan)
            ->getServiceBreadcrumb($this->getRecord());
    }

    public function form(Form $form): Form
    {
        $counselingSheet = $this->record->organizationService->service->counseling_sheet;
        $schema = [];

        if (CounselingSheet::isValue($counselingSheet, CounselingSheet::LEGAL_ASSISTANCE)) {
            $schema = $this->getLegalAssistanceForm();
        }

        if (CounselingSheet::isValue($counselingSheet, CounselingSheet::PSYCHOLOGICAL_ASSISTANCE)) {
            $schema = $this->getSchemaForPsychologicalAssistance();
        }

        return $form->schema([
            Section::make()
                ->relationship('counselingSheet')
                ->maxWidth('3xl')
                ->schema($schema),
        ]);
    }

    private function getLegalAssistanceForm(): array
    {
        return [
            Section::make(__('intervention_plan.headings.patrimony_data'))
                ->columns()
                ->schema([
                    Select::make('data.patrimony')
                        ->label(__('intervention_plan.labels.patrimony')),
                    Select::make('data.possession_mode')
                        ->label(__('intervention_plan.labels.possession_mode')),
                    Textarea::make('data.possession_obse,rvation')
                        ->label(__('intervention_plan.labels.possession_observation'))
                        ->columnSpanFull(),
                ]),

            Section::make(__('intervention_plan.headings.file_documents'))
                ->columns()
                ->schema([
                    Group::make()
                        ->schema([
                            CheckboxList::make('data.original_documents')
                                ->label(__('intervention_plan.labels.original_documents')),
                            Textarea::make('data.original_documents_observation')
                                ->label(__('intervention_plan.labels.original_documents_observation')),
                        ]),

                    Group::make()
                        ->schema([
                            CheckboxList::make('data.copy_documents')
                                ->label(__('intervention_plan.labels.copy_documents')),
                            Textarea::make('data.copy_documents_observation')
                                ->label(__('intervention_plan.labels.copy_documents_observation')),
                        ]),
                ]),

            Section::make(__('intervention_plan.headings.institution_contacted'))
                ->schema([
                    TableRepeater::make('data.institutions')
                        ->hiddenLabel()
                        ->hideLabels()
                        ->schema([
                            TextInput::make('institution')
                                ->label(__('intervention_plan.labels.responsible_institution')),
                            DatePicker::make('contact_date')
                                ->label(__('intervention_plan.labels.contact_date')),
                            TextInput::make('phone')
                                ->label(__('intervention_plan.labels.phone')),
                            TextInput::make('contact_person')
                                ->label(__('intervention_plan.labels.contact_person')),
                        ]),

                ]),

            Section::make(__('intervention_plan.headings.final_observations'))
                ->schema([
                    Textarea::make('data.observations')
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
                    Grid::make()
                        ->columnSpanFull()
                        ->schema([
                            Select::make('data.substance_use')
                                ->label(__('intervention_plan.labels.substance_use'))
                                ->options(Ternary::options())
                                ->live(),
                            Select::make('data.substance_types')
                                ->label(__('intervention_plan.labels.substance_types'))
                                ->options(Drug::options())
                                ->visible(fn (Get $get) => Ternary::isYes($get('data.substance_use')))
                                ->live(),

                            TextInput::make('data.observations_substances')
                                ->label(__('intervention_plan.labels.observations_substances'))
                                ->maxLength(100)
                                ->visible(fn (Get $get) => Drug::isValue($get('data.substance_types'), Drug::OTHER))
                                ->columnSpanFull(),
                        ]),

                    //                    TextInput::make('current_contraception')
                    //                        ->label(__('intervention_plan.labels.current_contraception')),
                    //                    TextInput::make('observations_contraception')
                    //                        ->label(__('intervention_plan.labels.observations_contraception')),
                    Select::make('data.psychiatric_history')
                        ->label(__('intervention_plan.labels.psychiatric_history'))
                        ->options(Ternary::options()),
                    TextInput::make('data.psychiatric_history_observations')
                        ->label(__('intervention_plan.labels.psychiatric_history_observations'))
                        ->maxLength(100),
                    Select::make('data.investigations_for_psychiatric_pathology')
                        ->label(__('intervention_plan.labels.investigations_for_psychiatric_pathology'))
                        ->options(Ternary::options()),
                    TextInput::make('data.investigations_observations')
                        ->label(__('intervention_plan.labels.investigations_observations'))
                        ->maxLength(100),
                    Select::make('data.treatment_for_psychiatric_pathology')
                        ->label(__('intervention_plan.labels.treatment_for_psychiatric_pathology'))
                        ->options(Ternary::options()),
                    TextInput::make('data.treatment_observations')
                        ->label(__('intervention_plan.labels.treatment_observations'))
                        ->maxLength(100),
                ]),

            Section::make(__('intervention_plan.headings.history'))
                ->schema([
                    Textarea::make('data.relationship_history')
                        ->label(__('intervention_plan.labels.relationship_history'))
                        ->maxLength(1500),
                    Textarea::make('data.last_incident_description')
                        ->label(__('intervention_plan.labels.last_incident_description'))
                        ->maxLength(1500),
                ]),

            Section::make(__('intervention_plan.headings.violence_forms'))
                ->schema([
                    Textarea::make('data.violence_history_forms')
                        ->label(__('intervention_plan.labels.violence_history_forms'))
                        ->maxLength(1500),
                    //TODO radio table, maybe custom component
                    Select::make('data.physics')
                        ->label(__('intervention_plan.labels.physics'))
                        ->options(Frequency::options()),
                    Select::make('data.sexed')
                        ->label(__('intervention_plan.labels.sexed'))
                        ->options(Frequency::options()),
                    Select::make('data.psychological')
                        ->label(__('intervention_plan.labels.psychological'))
                        ->options(Frequency::options()),
                    Select::make('data.verbal')
                        ->label(__('intervention_plan.labels.verbal'))
                        ->options(Frequency::options()),
                    Select::make('data.sociable')
                        ->label(__('intervention_plan.labels.sociable'))
                        ->options(Frequency::options()),
                    Select::make('data.economic')
                        ->label(__('intervention_plan.labels.economic'))
                        ->options(Frequency::options()),
                    Select::make('data.cybernetics')
                        ->label(__('intervention_plan.labels.cybernetics'))
                        ->options(Frequency::options()),
                    Select::make('data.spiritual')
                        ->label(__('intervention_plan.labels.spiritual'))
                        ->options(Frequency::options()),
                    Textarea::make('data.physical_violence_description')
                        ->label(__('intervention_plan.labels.physical_violence_description'))
                        ->maxLength(1500),
                    Textarea::make('data.sexual_violence_description')
                        ->label(__('intervention_plan.labels.sexual_violence_description'))
                        ->maxLength(1500),
                    Textarea::make('data.psychological_violence_description')
                        ->label(__('intervention_plan.labels.psychological_violence_description'))
                        ->maxLength(1500),
                    Textarea::make('data.verbal_violence_description')
                        ->label(__('intervention_plan.labels.verbal_violence_description'))
                        ->maxLength(1500),
                    Textarea::make('data.social_violence_description')
                        ->label(__('intervention_plan.labels.social_violence_description'))
                        ->maxLength(1500),
                    Textarea::make('data.economic_violence_description')
                        ->label(__('intervention_plan.labels.economic_violence_description'))
                        ->maxLength(1500),
                    Textarea::make('data.cyber_violence_description')
                        ->label(__('intervention_plan.labels.cyber_violence_description'))
                        ->maxLength(1500),
                    Textarea::make('data.spiritual_violence_description')
                        ->label(__('intervention_plan.labels.spiritual_violence_description'))
                        ->maxLength(1500),

                ]),

            Section::make(__('intervention_plan.headings.violence_effects'))
                ->schema([
                    Textarea::make('data.physical_effects')
                        ->label(__('intervention_plan.labels.physical_effects'))
                        ->maxLength(1500),
                    Textarea::make('data.psychological_effects')
                        ->label(__('intervention_plan.labels.psychological_effects'))
                        ->maxLength(1500),
                    Textarea::make('data.social_effects')
                        ->label(__('intervention_plan.labels.social_effects'))
                        ->maxLength(1500),
                ]),

            Section::make(__('intervention_plan.headings.risk_factors'))
                ->schema([
                    Textarea::make('data.risk_factors_description')
                        ->label(__('intervention_plan.labels.risk_factors_description'))
                        ->maxLength(1500),
                ]),

            Section::make(__('intervention_plan.headings.protection_factors'))
                ->schema([
                    Textarea::make('data.internal_resources')
                        ->label(__('intervention_plan.labels.internal_resources'))
                        ->maxLength(1500),
                    Textarea::make('data.external_resources')
                        ->label(__('intervention_plan.labels.external_resources'))
                        ->maxLength(1500),
                ]),

            Section::make(__('intervention_plan.headings.request'))
                ->schema([
                    Textarea::make('data.requests_description')
                        ->label(__('intervention_plan.labels.requests_description'))
                        ->maxLength(1500),
                ]),

            Section::make(__('intervention_plan.headings.psychological_evaluation'))
                ->schema([
                    Textarea::make('data.first_meeting_psychological_evaluation')
                        ->label(__('intervention_plan.labels.first_meeting_psychological_evaluation'))
                        ->maxLength(5000),
                ]),

        ];
    }
}
