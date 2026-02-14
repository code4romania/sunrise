<?php

declare(strict_types=1);

namespace App\Schemas;

use App\Enums\CounselingSheet;
use App\Enums\ExtendedFrequency;
use App\Enums\FamilyRelationship;
use App\Enums\FileDocumentType;
use App\Enums\HomeType;
use App\Enums\Patrimony;
use App\Enums\PossessionMode;
use App\Enums\SocialRelationship;
use App\Enums\Ternary;
use App\Infolists\Components\EnumEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;

class CounselingSheetInfolistSchemas
{
    /**
     * @return array<int, Section>
     */
    public static function getDisplaySchemaFor(CounselingSheet $type): array
    {
        return match ($type) {
            CounselingSheet::LEGAL_ASSISTANCE => self::legalAssistance(),
            CounselingSheet::PSYCHOLOGICAL_ASSISTANCE => self::psychologicalAssistance(),
            CounselingSheet::SOCIAL_ASSISTANCE => self::socialAssistance(),
        };
    }

    /**
     * @return array<int, Section>
     */
    private static function legalAssistance(): array
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

    /**
     * @return array<int, Section>
     */
    private static function psychologicalAssistance(): array
    {
        $frequencyFields = [
            'physics', 'sexed', 'psychological', 'verbal', 'sociable', 'economic', 'cybernetics', 'spiritual',
        ];

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
                        ->state(function ($record) use ($frequencyFields): string {
                            $lines = collect($frequencyFields)
                                ->map(function (string $field) use ($record): ?string {
                                    $frequency = ExtendedFrequency::tryFrom(
                                        (string) data_get($record, "data.{$field}")
                                    );
                                    if (blank($frequency)) {
                                        return null;
                                    }

                                    return sprintf(
                                        '%s - %s',
                                        __('intervention_plan.labels.'.$field),
                                        $frequency->getLabel()
                                    );
                                })
                                ->filter()
                                ->values()
                                ->all();

                            return implode("\n", $lines) ?: '—';
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

    /**
     * @return array<int, Section>
     */
    private static function socialAssistance(): array
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
}
