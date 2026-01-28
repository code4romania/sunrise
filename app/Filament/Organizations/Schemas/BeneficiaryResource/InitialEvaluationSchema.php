<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Schemas\BeneficiaryResource;

use App\Enums\AggravatingFactorsSchema;
use App\Enums\Frequency;
use App\Enums\Helps;
use App\Enums\RecommendationService;
use App\Enums\RiskFactorsSchema;
use App\Enums\Ternary;
use App\Enums\VictimPerceptionOfTheRiskSchema;
use App\Enums\Violence;
use App\Enums\ViolenceHistorySchema;
use App\Enums\ViolencesTypesSchema;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Select;
use App\Infolists\Components\DateEntry;
use App\Infolists\Components\EnumEntry;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;

class InitialEvaluationSchema
{
    public static function getEvaluationDetailsFormComponents(): array
    {
        return [
            Section::make()
                ->relationship('evaluateDetails')
                ->maxWidth('3xl')
                ->columns()
                ->schema([
                    DatePicker::make('registered_date')
                        ->label(__('beneficiary.section.initial_evaluation.labels.registered_date'))
                        ->required(),

                    TextInput::make('file_number')
                        ->label(__('beneficiary.section.initial_evaluation.labels.file_number'))
                        ->placeholder(__('beneficiary.placeholder.file_number'))
                        ->maxLength(50),

                    Select::make('specialist_id')
                        ->label(__('beneficiary.section.initial_evaluation.labels.specialist'))
                        ->placeholder(__('beneficiary.placeholder.specialist'))
                        ->required()
                        ->default(auth()->user()->id)
                        ->options(fn ($record) => User::getTenantOrganizationUsers()),

                    Textarea::make('method_of_identifying_the_service')
                        ->label(__('beneficiary.section.initial_evaluation.labels.method_of_identifying_the_service'))
                        ->placeholder(__('beneficiary.placeholder.method_of_identifying_the_service'))
                        ->columnSpanFull()
                        ->maxLength(2000),
                ]),
        ];
    }

    public static function getEvaluationDetailsInfolistComponents(): array
    {
        return [
            Group::make()
                ->columns()
                ->relationship('evaluateDetails')
                ->schema([
                    DateEntry::make('registered_date')
                        ->label(__('beneficiary.section.initial_evaluation.labels.registered_date')),

                    TextEntry::make('file_number')
                        ->label(__('beneficiary.section.initial_evaluation.labels.file_number'))
                        ->placeholder(__('beneficiary.placeholder.file_number')),

                    TextEntry::make('specialist.full_name')
                        ->label(__('beneficiary.section.initial_evaluation.labels.specialist'))
                        ->placeholder(__('beneficiary.placeholder.specialist')),

                    TextEntry::make('method_of_identifying_the_service')
                        ->label(__('beneficiary.section.initial_evaluation.labels.method_of_identifying_the_service'))
                        ->placeholder(__('beneficiary.placeholder.method_of_identifying_the_service'))
                        ->columnSpanFull(),
                ]),
        ];
    }

    public static function getRiskFactorsFormComponents(): array
    {
        return [
            Group::make()
                ->relationship('riskFactors')
                ->schema([
                    Section::make(__('beneficiary.section.initial_evaluation.heading.violence_history'))
                        ->schema(self::getViolenceHistorySchema()),
                    Section::make(__('beneficiary.section.initial_evaluation.heading.violences_types'))
                        ->schema(self::getViolencesTypesSchema()),
                    Section::make(__('beneficiary.section.initial_evaluation.heading.risk_factors'))
                        ->schema(self::getRiskFactorsSchema()),
                    Section::make(__('beneficiary.section.initial_evaluation.heading.victim_perception_of_the_risk'))
                        ->schema(self::getVictimPerceptionOfTheRiskSchema()),
                    Section::make(__('beneficiary.section.initial_evaluation.heading.aggravating_factors'))
                        ->schema(self::getAggravatingFactorsSchema()),
                    Section::make(__('beneficiary.section.initial_evaluation.heading.social_support'))
                        ->columns()
                        ->schema(self::getSocialSupportSchema()),
                ]),
        ];
    }

    public static function getViolenceFormComponents(): array
    {
        return [
            Section::make()
                ->relationship('violence')
                ->maxWidth('3xl')
                ->columns()
                ->schema([
                    Select::make('violence_types')
                        ->label(__('beneficiary.section.initial_evaluation.labels.violence_type'))
                        ->placeholder(__('beneficiary.placeholder.violence_type'))
                        ->options(Violence::options())
                        ->multiple()
                        ->required(),

                    Select::make('violence_primary_type')
                        ->label(__('beneficiary.section.initial_evaluation.labels.violence_primary_type'))
                        ->placeholder(__('beneficiary.placeholder.violence_primary_type'))
                        ->options(Violence::options())
                        ->required(),

                    Select::make('frequency_violence')
                        ->label(__('beneficiary.section.initial_evaluation.labels.frequency_violence'))
                        ->placeholder(__('beneficiary.placeholder.frequency_violence'))
                        ->options(Frequency::options())
                        ->required(),

                    RichEditor::make('description')
                        ->label(__('beneficiary.section.initial_evaluation.labels.description'))
                        ->placeholder(__('beneficiary.placeholder.description'))
                        ->helperText(__('beneficiary.helper_text.violence_description'))
                        ->columnSpanFull()
                        ->maxLength(5000),
                ]),
        ];
    }

    public static function getViolenceInfolistComponents(): array
    {
        return [
            Group::make()
                ->relationship('violence')
                ->columns()
                ->schema([
                    TextEntry::make('violence_types')
                        ->label(__('beneficiary.section.initial_evaluation.labels.violence_type')),

                    EnumEntry::make('violence_primary_type')
                        ->label(__('beneficiary.section.initial_evaluation.labels.violence_primary_type'))
                        ->placeholder(__('beneficiary.placeholder.violence_primary_type')),

                    EnumEntry::make('frequency_violence')
                        ->label(__('beneficiary.section.initial_evaluation.labels.frequency_violence'))
                        ->placeholder(__('beneficiary.placeholder.frequency_violence')),

                    TextEntry::make('description')
                        ->label(__('beneficiary.section.initial_evaluation.labels.description'))
                        ->placeholder(__('beneficiary.placeholder.description'))
                        ->columnSpanFull()
                        ->html(),
                ]),
        ];
    }

    public static function getRequestedServicesFormComponents(): array
    {
        return [
            Group::make()
                ->relationship('requestedServices')
                ->schema([
                    Section::make(__('beneficiary.section.initial_evaluation.heading.types_of_requested_services'))
                        ->schema(self::getRequestedServicesSchema()),
                ]),
        ];
    }

    public static function getRequestedServicesSchema(): array
    {
        return [
            CheckboxList::make('requested_services')
                ->hiddenLabel()
                ->options(RecommendationService::options()),

            Textarea::make('other_services_description')
                ->hiddenLabel()
                ->placeholder(__('beneficiary.placeholder.other_services'))
                ->maxLength(100),
        ];
    }

    public static function getBeneficiarySituationFormComponents(): array
    {
        return [
            Section::make()
                ->relationship('beneficiarySituation')
                ->maxWidth('3xl')
                ->schema([
                    TextInput::make('moment_of_evaluation')
                        ->label(__('beneficiary.section.initial_evaluation.labels.moment_of_evaluation'))
                        ->placeholder(__('beneficiary.placeholder.moment_of_evaluation'))
                        ->maxLength(100),

                    RichEditor::make('description_of_situation')
                        ->label(__('beneficiary.section.initial_evaluation.labels.description_of_situation'))
                        ->placeholder(__('beneficiary.placeholder.description_of_situation'))
                        ->maxLength(5000),
                ]),
        ];
    }

    public static function getBeneficiarySituationInfolistComponents(): array
    {
        return [
            Group::make()
                ->relationship('beneficiarySituation')
                ->schema([
                    TextEntry::make('moment_of_evaluation')
                        ->label(__('beneficiary.section.initial_evaluation.labels.moment_of_evaluation')),

                    TextEntry::make('description_of_situation')
                        ->label(__('beneficiary.section.initial_evaluation.labels.description_of_situation'))
                        ->html(),
                ]),
        ];
    }

    public static function getViolenceHistorySchema(): array
    {
        $enumOptions = ViolenceHistorySchema::options();

        return self::getSchemaFromEnum($enumOptions);
    }

    public static function getViolencesTypesSchema(): array
    {
        $enumOptions = ViolencesTypesSchema::options();

        return self::getSchemaFromEnum($enumOptions);
    }

    public static function getRiskFactorsSchema(): array
    {
        $enumOptions = RiskFactorsSchema::options();

        return self::getSchemaFromEnum($enumOptions);
    }

    public static function getVictimPerceptionOfTheRiskSchema(): array
    {
        $enumOptions = VictimPerceptionOfTheRiskSchema::options();

        return self::getSchemaFromEnum($enumOptions);
    }

    public static function getAggravatingFactorsSchema(): array
    {
        $enumOptions = AggravatingFactorsSchema::options();

        return self::getSchemaFromEnum($enumOptions);
    }

    public static function getSocialSupportSchema(): array
    {
        return [
            Group::make()
                ->schema([
                    Select::make('extended_family_can_provide')
                        ->label(__('beneficiary.section.initial_evaluation.labels.extended_family_can_provide'))
                        ->multiple()
                        ->options(Helps::options())
                        ->disabled(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('extended_family_can_not_provide')),

                    Checkbox::make('extended_family_can_not_provide')
                        ->label(__('beneficiary.section.initial_evaluation.labels.extended_family_can_not_provide'))
                        ->afterStateUpdated(
                            function ($state, \Filament\Schemas\Components\Utilities\Get $get, \Filament\Schemas\Components\Utilities\Set $set) {
                                if ($state) {
                                    $set('extended_family_can_provide_old_values', $get('extended_family_can_provide'));
                                    $set('extended_family_can_provide', null);

                                    return;
                                }
                                $set('extended_family_can_provide', $get('extended_family_can_provide_old_values'));
                            }
                        )
                        ->live(),

                    Hidden::make('extended_family_can_provide_old_values'),
                ]),

            Group::make()
                ->schema([
                    Select::make('friends_can_provide')
                        ->label(__('beneficiary.section.initial_evaluation.labels.friends_can_provide'))
                        ->multiple()
                        ->options(Helps::options())
                        ->disabled(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('friends_can_not_provide')),

                    Checkbox::make('friends_can_not_provide')
                        ->label(__('beneficiary.section.initial_evaluation.labels.friends_can_not_provide'))
                        ->afterStateUpdated(
                            function ($state, \Filament\Schemas\Components\Utilities\Get $get, \Filament\Schemas\Components\Utilities\Set $set) {
                                if ($state) {
                                    $set('friends_can_provide_old_values', $get('friends_can_provide'));
                                    $set('friends_can_provide', null);

                                    return;
                                }
                                $set('friends_can_provide', $get('friends_can_provide_old_values'));
                            }
                        )
                        ->live(),

                    Hidden::make('friends_can_provide_old_values'),
                ]),
        ];
    }

    public static function getSchemaFromEnum(array $enumOptions): array
    {
        $fields = [];
        foreach ($enumOptions as $key => $value) {
            $fields[] = Radio::make('risk_factors.' . $key . '.value')
                ->label($value)
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options())
                ->enum(Ternary::class);
            $fields[] = TextInput::make('risk_factors.' . $key . '.description')
                ->hiddenLabel()
                ->placeholder(__('beneficiary.placeholder.observations'))
                ->maxLength(100);
        }

        return $fields;
    }

    public static function getInfolistSchemaFromEnum(array $enumOptions): array
    {
        $fields = [];
        foreach ($enumOptions as $key => $value) {
            $fields[] = TextEntry::make('risk_factors.' . $key . '.value')
                ->label($value)
                ->formatStateUsing(function ($record, $state) use ($key) {
                    $result = $state !== '-'
                        ? Ternary::tryFrom((int) $state)?->label()
                        : null;

                    $result ??= '-';

                    $description = data_get($record->riskFactors->risk_factors, "{$key}.description");

                    if (filled($description)) {
                        $result .= " ({$description})";
                    }

                    return $result;
                })
                ->inlineLabel(false);
        }

        return $fields;
    }
}
