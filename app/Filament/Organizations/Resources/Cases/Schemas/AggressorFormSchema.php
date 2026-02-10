<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Schemas;

use App\Enums\AggressorLegalHistory;
use App\Enums\AggressorRelationship;
use App\Enums\Citizenship;
use App\Enums\CivilStatus;
use App\Enums\Drug;
use App\Enums\Gender;
use App\Enums\Occupation;
use App\Enums\ProtectionOrder;
use App\Enums\Studies;
use App\Enums\Ternary;
use App\Enums\Violence;
use App\Forms\Components\Select;
use App\Rules\MultipleIn;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;

class AggressorFormSchema
{
    /**
     * Schema for one aggressor item (used in Repeater on edit page and create wizard).
     *
     * @return array<int, mixed>
     */
    public static function getRepeaterItemSchema(): array
    {
        return [
            Select::make('relationship')
                ->label(__('field.aggressor_relationship'))
                ->placeholder(__('placeholder.select_one'))
                ->options(AggressorRelationship::options())
                ->enum(AggressorRelationship::class)
                ->live(),

            Textarea::make('relationship_other')
                ->label(__('field.aggressor_relationship_other'))
                ->placeholder(__('placeholder.input_text'))
                ->visible(fn (Get $get): bool => AggressorRelationship::isValue($get('relationship'), AggressorRelationship::OTHER))
                ->maxLength(100),

            TextInput::make('age')
                ->label(__('field.aggressor_age'))
                ->placeholder(__('placeholder.number'))
                ->numeric()
                ->minValue(0)
                ->maxValue(99),

            Select::make('gender')
                ->label(__('field.aggressor_gender'))
                ->placeholder(__('placeholder.select_one'))
                ->options(Gender::options())
                ->enum(Gender::class),

            Select::make('citizenship')
                ->label(__('field.aggressor_citizenship'))
                ->placeholder(__('placeholder.citizenship'))
                ->options(Citizenship::options())
                ->nullable(),

            Select::make('civil_status')
                ->label(__('field.aggressor_civil_status'))
                ->placeholder(__('placeholder.civil_status'))
                ->options(CivilStatus::options())
                ->enum(CivilStatus::class),

            Select::make('studies')
                ->label(__('field.aggressor_studies'))
                ->placeholder(__('placeholder.studies'))
                ->options(Studies::options())
                ->enum(Studies::class),

            Select::make('occupation')
                ->label(__('field.aggressor_occupation'))
                ->placeholder(__('placeholder.select_one'))
                ->options(Occupation::options())
                ->enum(Occupation::class),

            Group::make([
                Select::make('has_violence_history')
                    ->label(__('field.aggressor_has_violence_history'))
                    ->placeholder(__('placeholder.select_one'))
                    ->options(Ternary::options())
                    ->enum(Ternary::class)
                    ->live(),
                Group::make()
                    ->visible(fn (Get $get): bool => Ternary::isYes($get('has_violence_history')))
                    ->columns(1)
                    ->schema([
                        Select::make('violence_types')
                            ->label(__('field.aggressor_violence_types'))
                            ->placeholder(__('placeholder.select_many'))
                            ->options(Violence::options())
                            ->rule(new MultipleIn(Violence::values()))
                            ->multiple(),
                        Select::make('legal_history')
                            ->label(__('field.aggressor_legal_history'))
                            ->placeholder(__('placeholder.select_many'))
                            ->options(AggressorLegalHistory::options())
                            ->rule(new MultipleIn(AggressorLegalHistory::values()))
                            ->multiple()
                            ->live(),
                        Textarea::make('legal_history_notes')
                            ->label(__('field.aggressor_legal_history_notes'))
                            ->placeholder(__('placeholder.input_text'))
                            ->maxLength(100),
                    ]),
            ])->columnSpanFull()->columns(2),

            Group::make()
                ->schema([
                    Select::make('has_protection_order')
                        ->label(__('field.has_protection_order'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(ProtectionOrder::options())
                        ->enum(ProtectionOrder::class)
                        ->live(),
                    Group::make()
                        ->columns(1)
                        ->schema([
                            Select::make('electronically_monitored')
                                ->label(__('field.electronically_monitored'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(Ternary::options())
                                ->enum(Ternary::class)
                                ->visible(
                                    fn (Get $get): bool => ProtectionOrder::ISSUED_BY_COURT->is($get('has_protection_order')) ||
                                        ProtectionOrder::TEMPORARY->is($get('has_protection_order'))
                                ),

                            TextInput::make('protection_order_notes')
                                ->label(__('field.protection_order_notes'))
                                ->placeholder(__('placeholder.input_text'))
                                ->visible(
                                    fn (Get $get): bool => ! ProtectionOrder::NO->is($get('has_protection_order')) &&
                                        ! ProtectionOrder::UNKNOWN->is($get('has_protection_order'))
                                )
                                ->maxLength(100),
                        ]),

                ])
                ->columnSpanFull()
                ->columns(2),

            Group::make()
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Select::make('has_psychiatric_history')
                        ->label(__('field.aggressor_has_psychiatric_history'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(Ternary::options())
                        ->enum(Ternary::class)
                        ->live(),

                    TextInput::make('psychiatric_history_notes')
                        ->label(__('field.aggressor_psychiatric_history_notes'))
                        ->placeholder(__('placeholder.input_text'))
                        ->maxLength(100)
                        ->visible(fn (Get $get): bool => Ternary::isYes($get('has_psychiatric_history'))),
                ]),

            Group::make()
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Select::make('has_drug_history')
                        ->label(__('field.aggressor_has_drug_history'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(Ternary::options())
                        ->enum(Ternary::class)
                        ->live(),

                    Select::make('drugs')
                        ->label(__('field.aggressor_drugs'))
                        ->placeholder(__('placeholder.select_many'))
                        ->visible(fn (Get $get): bool => Ternary::isYes($get('has_drug_history')))
                        ->options(Drug::options())
                        ->rule(new MultipleIn(Drug::values()))
                        ->multiple(),
                ]),
        ];
    }
}
