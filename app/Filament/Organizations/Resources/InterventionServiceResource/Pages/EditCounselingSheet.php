<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionServiceResource\Pages;

use App\Enums\CounselingSheet;
use App\Enums\Drug;
use App\Enums\FileDocumentType;
use App\Enums\Frequency;
use App\Enums\Patrimony;
use App\Enums\PossessionMode;
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
                ->schema($schema),
        ]);
    }

    private function getLegalAssistanceForm(): array
    {
        return [
            Section::make(__('intervention_plan.headings.patrimony_data'))
                ->columns()
                ->maxWidth('3xl')
                ->schema([
                    Select::make('data.patrimony')
                        ->label(__('intervention_plan.labels.patrimony'))
                        ->options(Patrimony::options()),
                    Select::make('data.possession_mode')
                        ->label(__('intervention_plan.labels.possession_mode'))
                        ->options(PossessionMode::options())
                        ->live(),
                    Textarea::make('data.possession_observation')
                        ->label(__('intervention_plan.labels.possession_observation'))
                        ->visible(fn (Get $get) => PossessionMode::isValue($get('data.possession_mode'), PossessionMode::OTHER))
                        ->columnSpanFull(),
                ]),

            Section::make(__('intervention_plan.headings.file_documents'))
                ->columns()
                ->maxWidth('3xl')
                ->schema([
                    Group::make()
                        ->schema([
                            CheckboxList::make('data.original_documents')
                                ->label(__('intervention_plan.labels.original_documents'))
                                ->options(FileDocumentType::options())
                                ->live(),
                            Textarea::make('data.original_documents_observation')
                                ->label(__('intervention_plan.labels.original_documents_observation'))
                                ->visible(fn (Get $get) => \in_array(FileDocumentType::OTHER->value, $get('data.original_documents'))),
                        ]),

                    Group::make()
                        ->schema([
                            CheckboxList::make('data.copy_documents')
                                ->label(__('intervention_plan.labels.copy_documents'))
                                ->options(FileDocumentType::options())
                                ->live(),
                            Textarea::make('data.copy_documents_observation')
                                ->label(__('intervention_plan.labels.copy_documents_observation'))
                                ->visible(fn (Get $get) => \in_array(FileDocumentType::OTHER->value, $get('data.copy_documents'))),
                        ]),
                ]),

            Section::make(__('intervention_plan.headings.institution_contacted'))
                ->schema([
                    TableRepeater::make('data.institutions')
                        ->hiddenLabel()
                        ->hideLabels()
                        ->addActionLabel(__('intervention_plan.actions.add_institution'))
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
                ->maxWidth('3xl')
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
                ->maxWidth('3xl')
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

                    Select::make('data.current_contraception')
                        ->label(__('intervention_plan.labels.current_contraception'))
                        ->options(Ternary::options()),
                    TextInput::make('data.observations_contraception')
                        ->label(__('intervention_plan.labels.observations_contraception')),
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
                ->maxWidth('3xl')
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
                        ->maxLength(1500)
                        ->maxWidth('3xl'),
                    Radio::make('data.physics')
                        ->label(__('intervention_plan.labels.physics'))
                        ->inline()
                        ->options(Frequency::options()),
                    Radio::make('data.sexed')
                        ->label(__('intervention_plan.labels.sexed'))
                        ->inline()
                        ->options(Frequency::options()),
                    Radio::make('data.psychological')
                        ->label(__('intervention_plan.labels.psychological'))
                        ->inline()
                        ->options(Frequency::options()),
                    Radio::make('data.verbal')
                        ->label(__('intervention_plan.labels.verbal'))
                        ->inline()
                        ->options(Frequency::options()),
                    Radio::make('data.sociable')
                        ->label(__('intervention_plan.labels.sociable'))
                        ->inline()
                        ->options(Frequency::options()),
                    Radio::make('data.economic')
                        ->label(__('intervention_plan.labels.economic'))
                        ->inline()
                        ->options(Frequency::options()),
                    Radio::make('data.cybernetics')
                        ->label(__('intervention_plan.labels.cybernetics'))
                        ->inline()
                        ->options(Frequency::options()),
                    Radio::make('data.spiritual')
                        ->label(__('intervention_plan.labels.spiritual'))
                        ->inline()
                        ->options(Frequency::options()),
                    Group::make()
                        ->maxWidth('3xl')
                        ->schema([
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
                ]),

            Section::make(__('intervention_plan.headings.violence_effects'))
                ->maxWidth('3xl')
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
                ->maxWidth('3xl')
                ->schema([
                    Textarea::make('data.risk_factors_description')
                        ->label(__('intervention_plan.labels.risk_factors_description'))
                        ->maxLength(1500),
                ]),

            Section::make(__('intervention_plan.headings.protection_factors'))
                ->maxWidth('3xl')
                ->schema([
                    Textarea::make('data.internal_resources')
                        ->label(__('intervention_plan.labels.internal_resources'))
                        ->maxLength(1500),
                    Textarea::make('data.external_resources')
                        ->label(__('intervention_plan.labels.external_resources'))
                        ->maxLength(1500),
                ]),

            Section::make(__('intervention_plan.headings.request'))
                ->maxWidth('3xl')
                ->schema([
                    Textarea::make('data.requests_description')
                        ->label(__('intervention_plan.labels.requests_description'))
                        ->maxLength(1500),
                ]),

            Section::make(__('intervention_plan.headings.psychological_evaluation'))
                ->maxWidth('3xl')
                ->schema([
                    Textarea::make('data.first_meeting_psychological_evaluation')
                        ->label(__('intervention_plan.labels.first_meeting_psychological_evaluation'))
                        ->maxLength(5000),
                ]),

        ];
    }
}
