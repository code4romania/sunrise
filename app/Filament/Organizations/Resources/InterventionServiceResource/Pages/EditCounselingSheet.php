<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionServiceResource\Pages;

use App\Enums\CounselingSheet;
use App\Enums\ExtendedFrequency;
use App\Enums\FamilyRelationship;
use App\Enums\FileDocumentType;
use App\Enums\Gender;
use App\Enums\HomeType;
use App\Enums\Patrimony;
use App\Enums\PaymentMethod;
use App\Enums\PossessionMode;
use App\Enums\ProtectionMeasuringType;
use App\Enums\SocialRelationship;
use App\Enums\Ternary;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Filament\Organizations\Resources\InterventionServiceResource;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Repeater;
use App\Forms\Components\Select;
use App\Models\InterventionService;
use App\Services\Breadcrumb\InterventionPlanBreadcrumb;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditCounselingSheet extends EditRecord
{
    protected static string $resource = InterventionServiceResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('intervention_plan.headings.edit_counseling_sheet');
    }

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
        $counselingSheet = $this->record->organizationService->serviceWithoutStatusCondition->counseling_sheet;
        $schema = [];

        if (CounselingSheet::isValue($counselingSheet, CounselingSheet::LEGAL_ASSISTANCE)) {
            $schema = self::getLegalAssistanceForm();
        }

        if (CounselingSheet::isValue($counselingSheet, CounselingSheet::PSYCHOLOGICAL_ASSISTANCE)) {
            $schema = self::getSchemaForPsychologicalAssistance();
        }

        if (CounselingSheet::isValue($counselingSheet, CounselingSheet::SOCIAL_ASSISTANCE)) {
            $schema = self::getSchemaForSocialAssistance($this->getRecord());
        }

        return $form->schema([
            Section::make()
                ->relationship('counselingSheet')
                ->schema($schema),
        ]);
    }

    public static function getLegalAssistanceForm(): array
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
                        ->maxLength(100)
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
                                ->maxLength(100)
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
                                ->maxLength(100)
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
                                ->label(__('intervention_plan.labels.responsible_institution'))
                                ->maxLength(100),

                            DatePicker::make('contact_date')
                                ->label(__('intervention_plan.labels.contact_date')),

                            TextInput::make('phone')
                                ->label(__('intervention_plan.labels.phone'))
                                ->tel()
                                ->maxLength(14),

                            TextInput::make('contact_person')
                                ->label(__('intervention_plan.labels.contact_person'))
                                ->maxLength(100),
                        ]),

                ]),

            Section::make(__('intervention_plan.headings.final_observations'))
                ->maxWidth('3xl')
                ->schema([
                    Textarea::make('data.observations')
                        ->label(__('intervention_plan.labels.final_observation'))
                        ->maxLength(2500)
                        ->columnSpanFull(),
                ]),
        ];
    }

    public static function getSchemaForPsychologicalAssistance(): array
    {
        return [
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
                        ->options(ExtendedFrequency::options()),

                    Radio::make('data.sexed')
                        ->label(__('intervention_plan.labels.sexed'))
                        ->inline()
                        ->options(ExtendedFrequency::options()),

                    Radio::make('data.psychological')
                        ->label(__('intervention_plan.labels.psychological'))
                        ->inline()
                        ->options(ExtendedFrequency::options()),

                    Radio::make('data.verbal')
                        ->label(__('intervention_plan.labels.verbal'))
                        ->inline()
                        ->options(ExtendedFrequency::options()),

                    Radio::make('data.sociable')
                        ->label(__('intervention_plan.labels.sociable'))
                        ->inline()
                        ->options(ExtendedFrequency::options()),

                    Radio::make('data.economic')
                        ->label(__('intervention_plan.labels.economic'))
                        ->inline()
                        ->options(ExtendedFrequency::options()),

                    Radio::make('data.cybernetics')
                        ->label(__('intervention_plan.labels.cybernetics'))
                        ->inline()
                        ->options(ExtendedFrequency::options()),

                    Radio::make('data.spiritual')
                        ->label(__('intervention_plan.labels.spiritual'))
                        ->inline()
                        ->options(ExtendedFrequency::options()),

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

    public static function getSchemaForSocialAssistance(?InterventionService $interventionService = null): array
    {
        return [
            Section::make(__('intervention_plan.headings.family_relationship'))
                ->schema([
                    Repeater::make('data.family')
                        ->hiddenLabel()
                        ->itemLabel(function () {
                            static $index = 1;

                            return __('intervention_plan.labels.family', ['number' => $index++]);
                        })
                        ->columns()
                        ->addAction(
                            fn (Action $action) => $action->link()
                                ->label(__('intervention_plan.actions.add_social_relationship'))
                        )
                        ->reorderable(false)
                        ->maxWidth('3xl')
                        ->schema([

                            Select::make('relationship')
                                ->label(__('intervention_plan.labels.relationship'))
                                ->placeholder(__('intervention_plan.placeholders.select'))
                                ->options(FamilyRelationship::options()),

                            TextInput::make('first_and_last_name')
                                ->label(__('intervention_plan.labels.first_and_last_name'))
                                ->placeholder(__('intervention_plan.placeholders.first_and_last_name'))
                                ->maxLength(100),

                            TextInput::make('age')
                                ->label(__('intervention_plan.labels.age'))
                                ->placeholder(__('intervention_plan.placeholders.age'))
                                ->mask('999'),

                            TextInput::make('locality')
                                ->label(__('intervention_plan.labels.locality'))
                                ->placeholder(__('intervention_plan.placeholders.locality'))
                                ->maxLength(100),

                            TextInput::make('occupation')
                                ->label(__('intervention_plan.labels.occupation'))
                                ->placeholder(__('intervention_plan.placeholders.occupation'))
                                ->maxLength(100),

                            TextInput::make('relationship_observation')
                                ->label(__('intervention_plan.labels.relationship_observation'))
                                ->placeholder(__('intervention_plan.placeholders.relationship_observation'))
                                ->maxLength(250),

                            Select::make('support')
                                ->label(__('intervention_plan.labels.support'))
                                ->placeholder(__('intervention_plan.placeholders.support'))
                                ->options(Ternary::options()),

                            TextInput::make('support_observations')
                                ->label(__('intervention_plan.labels.support_observations'))
                                ->placeholder(__('intervention_plan.placeholders.observations'))
                                ->maxLength(250),

                        ]),
                ]),

            Section::make(__('intervention_plan.headings.support_group'))
                ->schema([
                    Repeater::make('data.support_group')
                        ->hiddenLabel()
                        ->itemLabel(function () {
                            static $index = 1;

                            return __('intervention_plan.labels.social_relationship', ['number' => $index++]);
                        })
                        ->columns()
                        ->addAction(
                            fn (Action $action) => $action->link()
                                ->label(__('intervention_plan.actions.add_social_relationship'))
                        )
                        ->reorderable(false)
                        ->maxWidth('3xl')
                        ->schema([
                            Select::make('relationship')
                                ->label(__('intervention_plan.labels.relationship'))
                                ->placeholder(__('intervention_plan.placeholders.select'))
                                ->options(SocialRelationship::options()),

                            TextInput::make('full_name')
                                ->label(__('intervention_plan.labels.person_or_group_name'))
                                ->placeholder(__('intervention_plan.placeholders.person_or_group_name'))
                                ->maxLength(200),

                            Select::make('support')
                                ->label(__('intervention_plan.labels.support'))
                                ->placeholder(__('intervention_plan.placeholders.select'))
                                ->options(Ternary::options()),

                            TextInput::make('support_observations')
                                ->label(__('intervention_plan.labels.support_observations'))
                                ->placeholder(__('intervention_plan.placeholders.support_observations'))
                                ->maxLength(250),
                        ]),
                ]),

            Section::make(__('intervention_plan.headings.living_conditions'))
                ->schema([
                    Grid::make()
                        ->maxWidth('3xl')
                        ->schema([
                            Select::make('data.home_type')
                                ->label(__('intervention_plan.labels.home_type'))
                                ->placeholder(__('intervention_plan.placeholders.select'))
                                ->options(HomeType::options()),

                            TextInput::make('data.rooms')
                                ->label(__('intervention_plan.labels.rooms'))
                                ->placeholder(__('intervention_plan.placeholders.rooms'))
                                ->mask('999'),

                            TextInput::make('data.peoples')
                                ->label(__('intervention_plan.labels.peoples'))
                                ->placeholder(__('intervention_plan.placeholders.peoples'))
                                ->mask('999'),

                            TextInput::make('data.utilities')
                                ->label(__('intervention_plan.labels.utilities'))
                                ->placeholder(__('intervention_plan.placeholders.observations'))
                                ->maxLength(250),

                            Textarea::make('data.living_observations')
                                ->label(__('intervention_plan.labels.living_observations'))
                                ->placeholder(__('intervention_plan.placeholders.add_details'))
                                ->columnSpanFull()
                                ->maxLength(1000),
                        ]),

                ]),

            Section::make(__('intervention_plan.headings.professional_experience'))
                ->schema([
                    Textarea::make('data.professional_experience')
                        ->label(__('intervention_plan.labels.professional_experience'))
                        ->placeholder(__('intervention_plan.placeholders.add_details'))
                        ->maxWidth('3xl')
                        ->maxLength(1000),
                ]),

            Section::make(__('intervention_plan.headings.children_details'))
                ->visible(fn () => ! $interventionService || $interventionService?->beneficiary->children->count())
                ->headerActions([
                    Action::make('view_children_identity')
                        ->label(__('intervention_plan.actions.view_children_identity'))
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn () => BeneficiaryResource::getUrl('view_identity', [
                            'record' => $interventionService->beneficiary,
                            'tab' => \sprintf('-%s-tab', Str::slug(__('beneficiary.section.identity.tab.children'))),
                        ]))
                        ->visible(fn () => $interventionService)
                        ->openUrlInNewTab()
                        ->link(),
                ])
                ->afterStateHydrated(function (Set $set, array $state) use ($interventionService) {
                    if (! $interventionService) {
                        return;
                    }

                    $childrenState = $state['data']['children'] ?? [];
                    $childrenState = collect($childrenState);
                    $beneficiaryChildren = $interventionService->beneficiary->children;

                    $children = [];
                    foreach ($beneficiaryChildren as $child) {
                        $childState = $childrenState->filter(fn (array $childState) => isset($childState['id']) && $childState['id'] === $child->id)
                            ->first() ?? [];
                        $children[] = array_merge($child->toArray(), $childState);
                    }

                    $set('data.children', $children);
                })
                ->schema([
                    Repeater::make('data.children')
                        ->hiddenLabel()
                        ->deletable(false)
                        ->reorderable(false)
                        ->addAction(fn (Action $action) => $action->hidden())
                        ->schema([
                            Grid::make()
                                ->columns(15)
                                ->schema([
                                    Placeholder::make('count')
                                        ->label(__('intervention_plan.labels.count'))
                                        ->content(function () {
                                            static $index = 1;

                                            return $index++;
                                        }),

                                    TextInput::make('name')
                                        ->label(__('intervention_plan.labels.children_name'))
                                        ->columnSpan(3)
                                        ->disabled(),

                                    TextInput::make('age')
                                        ->label(__('intervention_plan.labels.children_age'))
                                        ->disabled(),

                                    Select::make('gender')
                                        ->label(__('intervention_plan.labels.children_gender'))
                                        ->options(Gender::options())
                                        ->disabled(),

                                    TextInput::make('current_address')
                                        ->label(__('field.current_address'))
                                        ->columnSpan(3)
                                        ->disabled(),

                                    TextInput::make('status')
                                        ->label(__('field.child_status'))
                                        ->columnSpan(3)
                                        ->disabled(),

                                    TextInput::make('workspace')
                                        ->label(__('field.workspace'))
                                        ->columnSpan(3)
                                        ->disabled(),
                                ]),

                            Grid::make()
                                ->columns(3)
                                ->schema([
                                    Select::make('paternity_recognized')
                                        ->label(__('intervention_plan.labels.paternity_recognized'))
                                        ->placeholder(__('intervention_plan.placeholders.select'))
                                        ->options(Ternary::options()),

                                    Select::make('another_person_care')
                                        ->label(__('intervention_plan.labels.another_person_care'))
                                        ->placeholder(__('intervention_plan.placeholders.select'))
                                        ->options(Ternary::options()),

                                    TextInput::make('quality_person')
                                        ->label(__('intervention_plan.labels.quality_person'))
                                        ->placeholder(__('intervention_plan.placeholders.observations'))
                                        ->maxLength(100),

                                    Select::make('protection_measuring')
                                        ->label(__('intervention_plan.labels.protection_measuring'))
                                        ->placeholder(__('intervention_plan.placeholders.select'))
                                        ->options(Ternary::options())
                                        ->live(),

                                    Select::make('protection_measuring_type')
                                        ->label(__('intervention_plan.labels.protection_measuring_type'))
                                        ->placeholder(__('intervention_plan.placeholders.select'))
                                        ->options(ProtectionMeasuringType::options())
                                        ->visible(fn (Get $get) => Ternary::isYes($get('protection_measuring'))),

                                    Select::make('establishment_year')
                                        ->label(__('intervention_plan.labels.establishment_year'))
                                        ->placeholder(__('intervention_plan.placeholders.select_age'))
                                        ->options(function () {
                                            $options = [];
                                            for ($year = date('Y'); $year >= 1900; $year--) {
                                                $options[$year] = $year;
                                            }

                                            return $options;
                                        })
                                        ->default(date('Y'))
                                        ->visible(fn (Get $get) => Ternary::isYes($get('protection_measuring'))),
                                ]),

                            Grid::make()
                                ->columns(3)
                                ->schema([
                                    Select::make('allowance')
                                        ->label(__('intervention_plan.labels.allowance'))
                                        ->placeholder(__('intervention_plan.placeholders.select'))
                                        ->options(Ternary::options())
                                        ->live(),

                                    Select::make('payment_method')
                                        ->label(__('intervention_plan.labels.payment_method'))
                                        ->placeholder(__('intervention_plan.placeholders.select'))
                                        ->options(PaymentMethod::options())
                                        ->visible(fn (Get $get) => Ternary::isYes($get('allowance'))),
                                ]),

                            Grid::make()
                                ->columns(3)
                                ->schema([
                                    Select::make('family_medic')
                                        ->label(__('intervention_plan.labels.family_medic'))
                                        ->placeholder(__('intervention_plan.placeholders.select'))
                                        ->options(Ternary::options()),

                                    TextInput::make('family_doctor_contact')
                                        ->label(__('intervention_plan.labels.family_doctor_contact'))
                                        ->placeholder(__('intervention_plan.placeholders.observations'))
                                        ->maxLength(100),

                                    TextInput::make('health_status')
                                        ->label(__('intervention_plan.labels.health_status'))
                                        ->placeholder(__('intervention_plan.placeholders.observations'))
                                        ->maxLength(250),
                                ]),

                            TextInput::make('school_coordinator')
                                ->label(__('intervention_plan.labels.school_coordinator'))
                                ->placeholder(__('intervention_plan.placeholders.observations'))
                                ->maxLength(500),

                            Textarea::make('relationship_details')
                                ->label(__('intervention_plan.labels.relationship_details'))
                                ->placeholder(__('intervention_plan.placeholders.add_details'))
                                ->maxWidth('3xl')
                                ->maxLength(1000),
                        ]),
                ]),

            Section::make(__('intervention_plan.headings.integration_and_participation_in_social_service'))
                ->columns()
                ->maxWidth('3xl')
                ->schema([
                    Select::make('data.communication')
                        ->label(__('intervention_plan.labels.communication'))
                        ->placeholder(__('intervention_plan.placeholders.select'))
                        ->options(Ternary::options()),

                    TextInput::make('data.communication_observations')
                        ->label(__('intervention_plan.labels.communication_observations'))
                        ->placeholder(__('intervention_plan.placeholders.observations'))
                        ->maxLength(100),

                    Select::make('data.socialization')
                        ->label(__('intervention_plan.labels.socialization'))
                        ->placeholder(__('intervention_plan.placeholders.select'))
                        ->options(Ternary::options()),

                    TextInput::make('data.socialization_observations')
                        ->label(__('intervention_plan.labels.socialization_observations'))
                        ->placeholder(__('intervention_plan.placeholders.observations'))
                        ->maxLength(100),

                    Select::make('data.rules_compliance')
                        ->label(__('intervention_plan.labels.rules_compliance'))
                        ->placeholder(__('intervention_plan.placeholders.select'))
                        ->options(Ternary::options()),

                    TextInput::make('data.rules_compliance_observations')
                        ->label(__('intervention_plan.labels.rules_compliance_observations'))
                        ->placeholder(__('intervention_plan.placeholders.observations'))
                        ->maxLength(100),

                    Select::make('data.participation_in_individual_counseling')
                        ->label(__('intervention_plan.labels.participation_in_individual_counseling'))
                        ->placeholder(__('intervention_plan.placeholders.select'))
                        ->options(Ternary::options()),

                    TextInput::make('data.participation_in_individual_counseling_observations')
                        ->label(__('intervention_plan.labels.participation_in_individual_counseling_observations'))
                        ->placeholder(__('intervention_plan.placeholders.observations'))
                        ->maxLength(100),

                    Select::make('data.participation_in_joint_activities')
                        ->label(__('intervention_plan.labels.participation_in_joint_activities'))
                        ->placeholder(__('intervention_plan.placeholders.select'))
                        ->options(Ternary::options()),

                    TextInput::make('data.participation_in_joint_activities_observations')
                        ->label(__('intervention_plan.labels.participation_in_joint_activities_observations'))
                        ->placeholder(__('intervention_plan.placeholders.observations'))
                        ->maxLength(100),

                    Select::make('data.self_management')
                        ->label(__('intervention_plan.labels.self_management'))
                        ->placeholder(__('intervention_plan.placeholders.select'))
                        ->options(Ternary::options()),

                    TextInput::make('data.self_management_observations')
                        ->label(__('intervention_plan.labels.self_management_observations'))
                        ->placeholder(__('intervention_plan.placeholders.observations'))
                        ->maxLength(100),

                    Select::make('data.addictive_behavior')
                        ->label(__('intervention_plan.labels.addictive_behavior'))
                        ->placeholder(__('intervention_plan.placeholders.select'))
                        ->options(Ternary::options()),

                    TextInput::make('data.addictive_behavior_observations')
                        ->label(__('intervention_plan.labels.addictive_behavior_observations'))
                        ->placeholder(__('intervention_plan.placeholders.observations'))
                        ->maxLength(100),

                    Select::make('data.financial_education')
                        ->label(__('intervention_plan.labels.financial_education'))
                        ->placeholder(__('intervention_plan.placeholders.select'))
                        ->options(Ternary::options()),

                    TextInput::make('data.financial_education_observations')
                        ->label(__('intervention_plan.labels.financial_education_observations'))
                        ->placeholder(__('intervention_plan.placeholders.observations'))
                        ->maxLength(100),

                    Textarea::make('data.integration_and_participation_in_social_service_observations')
                        ->label(__('intervention_plan.labels.integration_and_participation_in_social_service_observations'))
                        ->placeholder(__('intervention_plan.placeholders.observations'))
                        ->columnSpanFull()
                        ->maxLength(1000),
                ]),
        ];
    }
}
