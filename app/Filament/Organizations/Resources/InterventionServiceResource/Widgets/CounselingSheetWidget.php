<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionServiceResource\Widgets;

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
use App\Infolists\Components\Actions\EditAction;
use App\Infolists\Components\EnumEntry;
use App\Infolists\Components\SectionHeader;
use App\Models\InterventionService;
use App\Widgets\InfolistWidget;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;

class CounselingSheetWidget extends InfolistWidget
{
    public ?InterventionService $record = null;

    public static function canView(): bool
    {
        $previousUrl = url()->previous();
        $parameters = explode('/', $previousUrl);
        $interventionService = InterventionService::find(end($parameters));

        return (bool) $interventionService?->organizationServiceWithoutStatusCondition
            ->serviceWithoutStatusCondition
            ->counseling_sheet;
    }

    protected function getInfoListSchema(): array
    {
        $counselingSheet = $this->record
            ->organizationServiceWithoutStatusCondition
            ->serviceWithoutStatusCondition
            ->counseling_sheet;

        if (blank($counselingSheet)) {
            return [];
        }

        $schema = match ($counselingSheet) {
            CounselingSheet::LEGAL_ASSISTANCE => $this->getSchemaForLegalAssistance(),
            CounselingSheet::PSYCHOLOGICAL_ASSISTANCE => $this->getSchemaForPsychologicalAssistance(),
            CounselingSheet::SOCIAL_ASSISTANCE => $this->getSchemaForSocialAssistance(),
            default => [],
        };

        $schema = $this->getSchemaForSocialAssistance();

        return [
            Section::make()
                ->relationship('counselingSheet')
                ->maxWidth(! CounselingSheet::isValue($counselingSheet, CounselingSheet::SOCIAL_ASSISTANCE) ? '3xl' : null)
                ->heading(__('intervention_plan.headings.counseling_sheet'))
                ->headerActions([
                    EditAction::make()
                        ->url(InterventionPlanResource::getUrl(
                            'edit_counseling_sheet',
                            [
                                'parent' => $this->record->interventionPlan,
                                'record' => $this->record,
                            ]
                        )),
                ])
                ->compact()
                ->schema($schema),
        ];
    }

    private function getSchemaForLegalAssistance(): array
    {
        return [
            Section::make(__('intervention_plan.headings.patrimony_data'))
                ->compact()
                ->columns()
                ->schema([
                    EnumEntry::make('data.patrimony')
                        ->label(__('intervention_plan.labels.patrimony'))
                        ->enumClass(Patrimony::class),
                    EnumEntry::make('data.possession_mode')
                        ->label(__('intervention_plan.labels.possession_mode'))
                        ->enumClass(PossessionMode::class),
                    TextEntry::make('data.possession_observation')
                        ->label(__('intervention_plan.labels.possession_observation')),
                ]),

            Section::make(__('intervention_plan.headings.file_documents'))
                ->compact()
                ->columns()
                ->schema([
                    Group::make()
                        ->schema([
                            EnumEntry::make('data.copy_documents')
                                ->label(__('intervention_plan.labels.copy_documents'))
                                ->enumClass(FileDocumentType::class),
                            TextEntry::make('data.copy_documents_observation')
                                ->label(__('intervention_plan.labels.copy_documents_observation')),
                        ]),
                    Group::make()
                        ->schema([
                            EnumEntry::make('data.original_documents')
                                ->label(__('intervention_plan.labels.original_documents'))
                                ->enumClass(FileDocumentType::class),
                            TextEntry::make('data.original_documents_observation')
                                ->label(__('intervention_plan.labels.original_documents_observation')),
                        ]),

                ]),

            Section::make(__('intervention_plan.headings.institution_contacted'))
                ->compact()
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
                ->compact()
                ->schema([
                    TextEntry::make('data.observations')
                        ->label(__('intervention_plan.labels.final_observation'))
                        ->columnSpanFull()
                        ->html(),
                ]),

        ];
    }

    private function getSchemaForPsychologicalAssistance(): array
    {
        return [
            Section::make(__('intervention_plan.headings.history'))
                ->compact()
                ->schema([
                    TextEntry::make('data.relationship_history')
                        ->label(__('intervention_plan.labels.relationship_history')),

                    TextEntry::make('data.last_incident_description')
                        ->label(__('intervention_plan.labels.last_incident_description')),
                ]),

            Section::make(__('intervention_plan.headings.violence_forms'))
                ->compact()
                ->schema([
                    TextEntry::make('data.violence_history_forms')
                        ->label(__('intervention_plan.labels.violence_history_forms')),

                    TextEntry::make('violence_frequency')
                        ->label(__('intervention_plan.labels.violence_frequency'))
                        ->state(function ($record) {
                            $fields = [
                                'physics',
                                'sexed',
                                'psychological',
                                'verbal',
                                'sociable',
                                'economic',
                                'cybernetics',
                                'spiritual',
                            ];

                            return collect($fields)
                                ->map(function (string $field) use ($record) {
                                    $frequency = ExtendedFrequency::tryFrom(
                                        (string) data_get($record->counselingSheet, "data.$field")
                                    );

                                    if (
                                        blank($frequency) ||
                                        $frequency->is(ExtendedFrequency::NO_ANSWER) ||
                                        $frequency->is(ExtendedFrequency::NONE)
                                    ) {
                                        return null;
                                    }

                                    return \sprintf(
                                        '%s - %s',
                                        __('intervention_plan.labels.' . $field),
                                        $frequency->getLabel()
                                    );
                                })
                                ->filter();
                        })
                        ->listWithLineBreaks(),

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
                ->compact()
                ->schema([
                    TextEntry::make('data.physical_effects')
                        ->label(__('intervention_plan.labels.physical_effects')),

                    TextEntry::make('data.psychological_effects')
                        ->label(__('intervention_plan.labels.psychological_effects')),

                    TextEntry::make('data.social_effects')
                        ->label(__('intervention_plan.labels.social_effects')),
                ]),

            Section::make(__('intervention_plan.headings.risk_factors'))
                ->compact()
                ->schema([
                    TextEntry::make('data.risk_factors_description')
                        ->label(__('intervention_plan.labels.risk_factors_description')),
                ]),

            Section::make(__('intervention_plan.headings.protection_factors'))
                ->compact()
                ->schema([
                    TextEntry::make('data.internal_resources')
                        ->label(__('intervention_plan.labels.internal_resources')),

                    TextEntry::make('data.external_resources')
                        ->label(__('intervention_plan.labels.external_resources')),
                ]),

            Section::make(__('intervention_plan.headings.request'))
                ->compact()
                ->schema([
                    TextEntry::make('data.requests_description')
                        ->label(__('intervention_plan.labels.requests_description')),
                ]),

            Section::make(__('intervention_plan.headings.psychological_evaluation'))
                ->compact()
                ->schema([
                    TextEntry::make('data.first_meeting_psychological_evaluation')
                        ->label(__('intervention_plan.labels.first_meeting_psychological_evaluation'))
                        ->html(),
                ]),
        ];
    }

    public function getSchemaForSocialAssistance(): array
    {
        return [
            Section::make(__('intervention_plan.headings.family_relationship'))
                ->maxWidth('3xl')
                ->compact()
                ->schema([
                    RepeatableEntry::make('data.family')
                        ->hiddenLabel()
                        ->columns()
                        ->schema([
                            SectionHeader::make('header')
                                ->state(function (SectionHeader $component) {
                                    $index = (int) explode('.', $component->getStatePath())[1];

                                    return __('intervention_plan.labels.family', ['number' => ++$index]);
                                }),

                            EnumEntry::make('relationship')
                                ->label(__('intervention_plan.labels.relationship'))
                                ->enumClass(FamilyRelationship::class),

                            TextEntry::make('first_and_last_name')
                                ->label(__('intervention_plan.labels.first_and_last_name')),

                            TextEntry::make('age')
                                ->label(__('intervention_plan.labels.age')),

                            TextEntry::make('locality')
                                ->label(__('intervention_plan.labels.locality')),

                            TextEntry::make('occupation')
                                ->label(__('intervention_plan.labels.occupation')),

                            TextEntry::make('relationship_observation')
                                ->label(__('intervention_plan.labels.relationship_observation')),

                            EnumEntry::make('support')
                                ->label(__('intervention_plan.labels.support'))
                                ->enumClass(Ternary::class),

                            TextEntry::make('support_observations')
                                ->label(__('intervention_plan.labels.support_observations')),

                        ]),
                ]),

            Section::make(__('intervention_plan.headings.support_group'))
                ->maxWidth('3xl')
                ->compact()
                ->schema([
                    RepeatableEntry::make('data.support_group')
                        ->hiddenLabel()
                        ->columns()
                        ->schema([
                            SectionHeader::make('header')
                                ->state(function (SectionHeader $component) {
                                    $index = (int) explode('.', $component->getStatePath())[1];

                                    return __('intervention_plan.labels.social_relationship', ['number' => ++$index]);
                                }),

                            EnumEntry::make('relationship')
                                ->label(__('intervention_plan.labels.relationship'))
                                ->enumClass(SocialRelationship::class),

                            TextEntry::make('full_name')
                                ->label(__('intervention_plan.labels.person_or_group_name')),

                            EnumEntry::make('support')
                                ->label(__('intervention_plan.labels.support'))
                                ->enumClass(Ternary::class),

                            TextEntry::make('support_observations')
                                ->label(__('intervention_plan.labels.support_observations')),
                        ]),
                ]),

            Section::make(__('intervention_plan.headings.living_conditions'))
                ->maxWidth('3xl')
                ->compact()
                ->schema([
                    Grid::make()
                        ->schema([
                            EnumEntry::make('data.home_type')
                                ->label(__('intervention_plan.labels.home_type'))
                                ->enumClass(HomeType::class),

                            TextEntry::make('data.rooms')
                                ->label(__('intervention_plan.labels.rooms')),

                            TextEntry::make('data.peoples')
                                ->label(__('intervention_plan.labels.peoples')),

                            TextEntry::make('data.utilities')
                                ->label(__('intervention_plan.labels.utilities')),

                            TextEntry::make('data.living_observations')
                                ->label(__('intervention_plan.labels.living_observations'))
                                ->columnSpanFull(),
                        ]),
                ]),

            Section::make(__('intervention_plan.headings.professional_experience'))
                ->maxWidth('3xl')
                ->compact()
                ->schema([
                    TextEntry::make('data.professional_experience')
                        ->label(__('intervention_plan.labels.professional_experience')),
                ]),

            Section::make(__('intervention_plan.headings.children_details'))
                ->visible(fn ($record) => $record->beneficiary->children->count())
                ->compact()
                ->headerActions([
                    \Filament\Actions\Action::make('view_children_identity')
                        ->label(__('intervention_plan.actions.view_children_identity'))
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn ($record) => BeneficiaryResource::getUrl('view_identity', [
                            'record' => $record->beneficiary,
                            'tab' => \sprintf('-%s-tab', Str::slug(__('beneficiary.section.identity.tab.children'))),
                        ]))
                        ->openUrlInNewTab()
                        ->link(),
                ])
                ->schema([
                    RepeatableEntry::make('data.children')
                        ->hiddenLabel()
                        ->schema([
                            Grid::make()
                                ->columns(15)
                                ->schema([
                                    TextEntry::make('count')
                                        ->label(__('intervention_plan.labels.count'))
                                        ->state(function () {
                                            static $index = 1;

                                            return $index++;
                                        }),

                                    TextEntry::make('name')
                                        ->label(__('intervention_plan.labels.children_name'))
                                        ->state(function (TextEntry $component, $record) {
                                            $index = (int) explode('.', $component->getStatePath())[3];

                                            return $record->beneficiary->children->get($index)->name;
                                        })
                                        ->columnSpan(3),

                                    TextEntry::make('age')
                                        ->label(__('intervention_plan.labels.children_age'))
                                        ->state(function (TextEntry $component, $record) {
                                            $index = (int) explode('.', $component->getStatePath())[3];

                                            return $record->beneficiary->children->get($index)->age;
                                        }),

                                    EnumEntry::make('gender')
                                        ->label(__('intervention_plan.labels.children_gender'))
                                        ->enumClass(Gender::class)
                                        ->state(function (TextEntry $component, $record) {
                                            $index = (int) explode('.', $component->getStatePath())[3];

                                            return $record->beneficiary->children->get($index)->gender;
                                        }),

                                    TextEntry::make('current_address')
                                        ->label(__('field.current_address'))
                                        ->state(function (TextEntry $component, $record) {
                                            $index = (int) explode('.', $component->getStatePath())[3];

                                            return $record->beneficiary->children->get($index)->current_address;
                                        })
                                        ->columnSpan(3),

                                    TextEntry::make('status')
                                        ->label(__('field.child_status'))
                                        ->state(function (TextEntry $component, $record) {
                                            $index = (int) explode('.', $component->getStatePath())[3];

                                            return $record->beneficiary->children->get($index)->status;
                                        })
                                        ->columnSpan(3),

                                    TextEntry::make('workspace')
                                        ->label(__('field.workspace'))
                                        ->state(function (TextEntry $component, $record) {
                                            $index = (int) explode('.', $component->getStatePath())[3];

                                            return $record->beneficiary->children->get($index)->workspace;
                                        })
                                        ->columnSpan(3),
                                ]),

                            Grid::make()
                                ->columns(3)
                                ->schema([
                                    EnumEntry::make('paternity_recognized')
                                        ->label(__('intervention_plan.labels.paternity_recognized'))
                                        ->enumClass(Ternary::class),

                                    EnumEntry::make('another_person_care')
                                        ->label(__('intervention_plan.labels.another_person_care'))
                                        ->enumClass(Ternary::class),

                                    TextEntry::make('quality_person')
                                        ->label(__('intervention_plan.labels.quality_person')),

                                    EnumEntry::make('protection_measuring')
                                        ->label(__('intervention_plan.labels.protection_measuring'))
                                        ->enumClass(Ternary::class),

                                    EnumEntry::make('protection_measuring_type')
                                        ->label(__('intervention_plan.labels.protection_measuring_type'))
                                        ->enumClass(ProtectionMeasuringType::class),

                                    EnumEntry::make('establishment_year')
                                        ->label(__('intervention_plan.labels.establishment_year')),
                                ]),

                            Grid::make()
                                ->columns(3)
                                ->schema([
                                    EnumEntry::make('allowance')
                                        ->label(__('intervention_plan.labels.allowance'))
                                        ->enumClass(Ternary::class),

                                    EnumEntry::make('payment_method')
                                        ->label(__('intervention_plan.labels.payment_method'))
                                        ->enumClass(PaymentMethod::class),
                                ]),

                            Grid::make()
                                ->columns(3)
                                ->schema([
                                    EnumEntry::make('family_medic')
                                        ->label(__('intervention_plan.labels.family_medic'))
                                        ->enumClass(Ternary::class),

                                    TextEntry::make('family_doctor_contact')
                                        ->label(__('intervention_plan.labels.family_doctor_contact')),

                                    TextEntry::make('health_status')
                                        ->label(__('intervention_plan.labels.health_status')),
                                ]),

                            TextEntry::make('school_coordinator')
                                ->label(__('intervention_plan.labels.school_coordinator')),

                            TextEntry::make('relationship_details')
                                ->label(__('intervention_plan.labels.relationship_details'))
                                ->maxWidth('3xl'),
                        ]),
                ]),

            Section::make(__('intervention_plan.headings.integration_and_participation_in_social_service'))
                ->columns()
                ->maxWidth('3xl')
                ->compact()
                ->schema([
                    EnumEntry::make('data.communication')
                        ->label(__('intervention_plan.labels.communication'))
                        ->enumClass(Ternary::class),

                    TextEntry::make('data.communication_observations')
                        ->label(__('intervention_plan.labels.communication_observations')),

                    EnumEntry::make('data.socialization')
                        ->label(__('intervention_plan.labels.socialization'))
                        ->enumClass(Ternary::class),

                    TextEntry::make('data.socialization_observations')
                        ->label(__('intervention_plan.labels.socialization_observations')),

                    EnumEntry::make('data.rules_compliance')
                        ->label(__('intervention_plan.labels.rules_compliance'))
                        ->enumClass(Ternary::class),

                    TextEntry::make('data.rules_compliance_observations')
                        ->label(__('intervention_plan.labels.rules_compliance_observations')),

                    EnumEntry::make('data.participation_in_individual_counseling')
                        ->label(__('intervention_plan.labels.participation_in_individual_counseling'))
                        ->enumClass(Ternary::class),

                    TextEntry::make('data.participation_in_individual_counseling_observations')
                        ->label(__('intervention_plan.labels.participation_in_individual_counseling_observations')),

                    EnumEntry::make('data.participation_in_joint_activities')
                        ->label(__('intervention_plan.labels.participation_in_joint_activities'))
                        ->enumClass(Ternary::class),

                    TextEntry::make('data.participation_in_joint_activities_observations')
                        ->label(__('intervention_plan.labels.participation_in_joint_activities_observations')),

                    EnumEntry::make('data.self_management')
                        ->label(__('intervention_plan.labels.self_management'))
                        ->enumClass(Ternary::class),

                    TextEntry::make('data.self_management_observations')
                        ->label(__('intervention_plan.labels.self_management_observations')),

                    EnumEntry::make('data.addictive_behavior')
                        ->label(__('intervention_plan.labels.addictive_behavior'))
                        ->enumClass(Ternary::class),

                    TextEntry::make('data.addictive_behavior_observations')
                        ->label(__('intervention_plan.labels.addictive_behavior_observations')),

                    EnumEntry::make('data.financial_education')
                        ->label(__('intervention_plan.labels.financial_education'))
                        ->enumClass(Ternary::class),

                    TextEntry::make('data.financial_education_observations')
                        ->label(__('intervention_plan.labels.financial_education_observations')),

                    TextEntry::make('data.integration_and_participation_in_social_service_observations')
                        ->label(__('intervention_plan.labels.integration_and_participation_in_social_service_observations'))
                        ->columnSpanFull(),
                ]),
        ];
    }

    public function getDisplayName(): string
    {
        return __('intervention_plan.headings.counseling_sheet');
    }
}
