<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Infolists\Components\EnumEntry;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\EditRecord;

class EditDetailedEvaluationResult extends EditRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function form(Form $form): Form
    {
        return $form->schema(self::getSchema());
    }

    public static function getSchema(): array
    {
        return [
            Group::make()
                ->relationship('detailedEvaluationResult')
                ->schema([
                    Section::make(__('beneficiary.section.detailed_evaluation.heading.recommendation_services'))
                        ->schema(self::getRecommendationServicesSchema()),
                    self::getInterventionPlanSchema(),

                ]),
        ];
    }

    public static function getRecommendationServicesSchema(): array
    {
        return [
            Checkbox::make('psychological_advice')
                ->label(__('beneficiary.section.detailed_evaluation.labels.psychological_advice')),
            Checkbox::make('legal_advice')
                ->label(__('beneficiary.section.detailed_evaluation.labels.legal_advice')),
            Checkbox::make('legal_assistance')
                ->label(__('beneficiary.section.detailed_evaluation.labels.legal_assistance')),
            Checkbox::make('family_counseling')
                ->label(__('beneficiary.section.detailed_evaluation.labels.family_counseling')),
            Checkbox::make('prenatal_advice')
                ->label(__('beneficiary.section.detailed_evaluation.labels.prenatal_advice')),
            Checkbox::make('social_advice')
                ->label(__('beneficiary.section.detailed_evaluation.labels.social_advice')),
            Checkbox::make('medical_services')
                ->label(__('beneficiary.section.detailed_evaluation.labels.medical_services')),
            Checkbox::make('medical_payment')
                ->label(__('beneficiary.section.detailed_evaluation.labels.medical_payment')),
            Checkbox::make('securing_residential_spaces')
                ->label(__('beneficiary.section.detailed_evaluation.labels.securing_residential_spaces')),
            Checkbox::make('occupational_program_services')
                ->label(__('beneficiary.section.detailed_evaluation.labels.occupational_program_services')),
            Checkbox::make('educational_services_for_children')
                ->label(__('beneficiary.section.detailed_evaluation.labels.educational_services_for_children')),
            Checkbox::make('temporary_shelter_services')
                ->label(__('beneficiary.section.detailed_evaluation.labels.temporary_shelter_services')),
            Checkbox::make('protection_order')
                ->label(__('beneficiary.section.detailed_evaluation.labels.protection_order')),
            Checkbox::make('crisis_assistance')
                ->label(__('beneficiary.section.detailed_evaluation.labels.crisis_assistance')),
            Checkbox::make('safety_plan')
                ->label(__('beneficiary.section.detailed_evaluation.labels.safety_plan')),
            Checkbox::make('other_services')
                ->label(__('beneficiary.section.detailed_evaluation.labels.other_services')),
            Textarea::make('other_services_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.other_services'))
                ->maxLength(100),
        ];
    }

    public static function getInterventionPlanSchema(): Section
    {
        return Section::make(__('beneficiary.section.detailed_evaluation.labels.recommendations_for_intervention_plan'))
            ->schema([
                RichEditor::make('recommendations_for_intervention_plan')
                    ->helperText(__('beneficiary.helper_text.recommendations_for_intervention_plan'))
                    ->label(__('beneficiary.section.detailed_evaluation.labels.recommendations_for_intervention_plan'))
                    ->placeholder(__('beneficiary.placeholder.recommendations_for_intervention_plan'))
                    ->maxLength(5000),
            ]);
    }

    public static function getRecommendationServicesInfolistSchema(): array
    {
        return [
            EnumEntry::make('psychological_advice')
                ->label(__('beneficiary.section.detailed_evaluation.labels.psychological_advice')),
            EnumEntry::make('legal_advice')
                ->label(__('beneficiary.section.detailed_evaluation.labels.legal_advice')),
            EnumEntry::make('legal_assistance')
                ->label(__('beneficiary.section.detailed_evaluation.labels.legal_assistance')),
            EnumEntry::make('family_counseling')
                ->label(__('beneficiary.section.detailed_evaluation.labels.family_counseling')),
            EnumEntry::make('prenatal_advice')
                ->label(__('beneficiary.section.detailed_evaluation.labels.prenatal_advice')),
            EnumEntry::make('social_advice')
                ->label(__('beneficiary.section.detailed_evaluation.labels.social_advice')),
            EnumEntry::make('medical_services')
                ->label(__('beneficiary.section.detailed_evaluation.labels.medical_services')),
            EnumEntry::make('medical_payment')
                ->label(__('beneficiary.section.detailed_evaluation.labels.medical_payment')),
            EnumEntry::make('securing_residential_spaces')
                ->label(__('beneficiary.section.detailed_evaluation.labels.securing_residential_spaces')),
            EnumEntry::make('occupational_program_services')
                ->label(__('beneficiary.section.detailed_evaluation.labels.occupational_program_services')),
            EnumEntry::make('educational_services_for_children')
                ->label(__('beneficiary.section.detailed_evaluation.labels.educational_services_for_children')),
            EnumEntry::make('temporary_shelter_services')
                ->label(__('beneficiary.section.detailed_evaluation.labels.temporary_shelter_services')),
            EnumEntry::make('protection_order')
                ->label(__('beneficiary.section.detailed_evaluation.labels.protection_order')),
            EnumEntry::make('crisis_assistance')
                ->label(__('beneficiary.section.detailed_evaluation.labels.crisis_assistance')),
            EnumEntry::make('safety_plan')
                ->label(__('beneficiary.section.detailed_evaluation.labels.safety_plan')),
            EnumEntry::make('other_services')
                ->label(__('beneficiary.section.detailed_evaluation.labels.other_services')),
            TextEntry::make('other_services_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.other_services')),
        ];
    }
}
