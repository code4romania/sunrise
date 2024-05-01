<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Enums\Helps;
use App\Enums\Ternary;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditRiskFactors extends EditRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function form(Form $form): Form
    {
        return $form->schema(self::getSchema());
    }

    public static function getSchema(): array
    {
        return [
            Group::make([
                Section::make(__('beneficiary.section.initial_evaluation.heading.violence_history'))
                    ->schema(self::getViolenceHistorySchema()),
                Section::make(__('beneficiary.section.initial_evaluation.heading.violences_types'))
                    ->schema(self::getViolencesTypesSchema()),
                Section::make(__('beneficiary.section.initial_evaluation.heading.heading_3'))
                    ->schema(self::getSection3Schema()),
                Section::make(__('beneficiary.section.initial_evaluation.heading.heading_4'))
                    ->schema(self::getSection4Schema()),
                Section::make(__('beneficiary.section.initial_evaluation.heading.heading_5'))
                    ->schema(self::getSection5Schema()),
                Section::make(__('beneficiary.section.initial_evaluation.heading.heading_6'))
                    ->columns()
                    ->schema(self::getSection6Schema()),
            ])
                ->relationship('riskFactors'),
        ];
    }

    public static function getViolenceHistorySchema(): array
    {
        return [
            Radio::make('previous_acts_of_violence')
                ->label(__('beneficiary.section.initial_evaluation.labels.previous_acts_of_violence'))
                ->inline()
                ->inlineLabel(false)->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('previous_acts_of_violence_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('violence_against_children_or_family_members')
                ->label(__('beneficiary.section.initial_evaluation.labels.violence_against_children_or_family_members'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('violence_against_children_or_family_members_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('abuser_exhibited_generalized_violent')
                ->label(__('beneficiary.section.initial_evaluation.labels.abuser_exhibited_generalized_violent'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('abuser_exhibited_generalized_violent_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('protection_order_in_past')
                ->label(__('beneficiary.section.initial_evaluation.labels.protection_order_in_past'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('protection_order_in_past_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('abuser_violated_protection_order')
                ->label(__('beneficiary.section.initial_evaluation.labels.abuser_violated_protection_order'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('abuser_violated_protection_order_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
        ];
    }

    public static function getViolencesTypesSchema(): array
    {
        return [
            Radio::make('frequency_of_violence_acts')
                ->label(__('beneficiary.section.initial_evaluation.labels.frequency_of_violence_acts'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('frequency_of_violence_acts_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('use_weapons_in_act_of_violence')
                ->label(__('beneficiary.section.initial_evaluation.labels.use_weapons_in_act_of_violence'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('use_weapons_in_act_of_violence_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('controlling_and_isolating')
                ->label(__('beneficiary.section.initial_evaluation.labels.controlling_and_isolating'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('controlling_and_isolating_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('stalked_or_harassed')
                ->label(__('beneficiary.section.initial_evaluation.labels.stalked_or_harassed'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('stalked_or_harassed_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('sexual_violence')
                ->label(__('beneficiary.section.initial_evaluation.labels.sexual_violence'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('sexual_violence_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('death_threats')
                ->label(__('beneficiary.section.initial_evaluation.labels.death_threats'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('death_threats_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('strangulation_attempt')
                ->label(__('beneficiary.section.initial_evaluation.labels.strangulation_attempt'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('strangulation_attempt_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
        ];
    }

    public static function getSection3Schema(): array
    {
        return [
            Radio::make('FR_S3Q1')
                ->label(__('beneficiary.section.initial_evaluation.labels.FR_S3Q1'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('FR_S3Q1_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('FR_S3Q2')
                ->label(__('beneficiary.section.initial_evaluation.labels.FR_S3Q2'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('FR_S3Q2_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('FR_S3Q3')
                ->label(__('beneficiary.section.initial_evaluation.labels.FR_S3Q3'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('FR_S3Q3_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('FR_S3Q4')
                ->label(__('beneficiary.section.initial_evaluation.labels.FR_S3Q4'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('FR_S3Q4_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
        ];
    }

    public static function getSection4Schema(): array
    {
        return [
            Radio::make('FR_S4Q1')
                ->label(__('beneficiary.section.initial_evaluation.labels.FR_S4Q1'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('FR_S4Q1_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('FR_S4Q2')
                ->label(__('beneficiary.section.initial_evaluation.labels.FR_S4Q2'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('FR_S4Q2_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
        ];
    }

    public static function getSection5Schema(): array
    {
        return [
            Radio::make('FR_S5Q1')
                ->label(__('beneficiary.section.initial_evaluation.labels.FR_S5Q1'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('FR_S5Q1_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('FR_S5Q2')
                ->label(__('beneficiary.section.initial_evaluation.labels.FR_S5Q2'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('FR_S5Q2_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('FR_S5Q3')
                ->label(__('beneficiary.section.initial_evaluation.labels.FR_S5Q3'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('FR_S5Q3_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('FR_S5Q4')
                ->label(__('beneficiary.section.initial_evaluation.labels.FR_S5Q4'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('FR_S5Q4_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),
            Radio::make('FR_S5Q5')
                ->label(__('beneficiary.section.initial_evaluation.labels.FR_S5Q5'))
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options()),
            TextInput::make('FR_S5Q5_description')
                ->label('')
                ->placeholder(__('beneficiary.placeholder.observations')),

        ];
    }

    public static function getSection6Schema(): array
    {
        return [
            Select::make('FR_S6Q1')
                ->label(__('beneficiary.section.initial_evaluation.labels.FR_S6Q1'))
                ->multiple()
                ->options(Helps::options()),
            Select::make('FR_S6Q2')
                ->label(__('beneficiary.section.initial_evaluation.labels.FR_S6Q2'))
                ->multiple()
                ->options(Helps::options()),
        ];
    }
}
