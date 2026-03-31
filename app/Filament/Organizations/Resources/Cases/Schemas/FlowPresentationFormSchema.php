<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Schemas;

use App\Enums\ActLocation;
use App\Enums\NotificationMode;
use App\Enums\Notifier;
use App\Enums\PresentationMode;
use App\Enums\ReferralMode;
use App\Forms\Components\Select;
use App\Models\ReferringInstitution;
use App\Rules\MultipleIn;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class FlowPresentationFormSchema
{
    /**
     * Schema for flow presentation in create case wizard (no record yet, so no relationship binding).
     *
     * @return array<int, mixed>
     */
    public static function getSchemaForCreateWizard(): array
    {
        $institutionOptions = fn () => ReferringInstitution::query()
            ->withoutGlobalScopes()
            ->orderBy('order')
            ->pluck('name', 'id')
            ->all();

        return [
            Group::make()
                ->statePath('flow_presentation')
                ->maxWidth('3xl')
                ->columns(2)
                ->schema([
                    Section::make(__('beneficiary.section.flow.presentation_and_referral'))
                        ->compact()
                        ->columnSpanFull()
                        ->columns(2)
                        ->schema([
                            Select::make('presentation_mode')
                                ->label(__('field.presentation_mode'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(PresentationMode::options())
                                ->enum(PresentationMode::class)
                                ->live(),
                            Group::make()
                                ->schema([
                                    Select::make('referring_institution_id')
                                        ->label(__('field.referring_institution'))
                                        ->placeholder(__('placeholder.select_one'))
                                        ->options($institutionOptions)
                                        ->visible(fn (Get $get) => PresentationMode::isValue(
                                            $get('presentation_mode'),
                                            PresentationMode::FORWARDED
                                        ))
                                        ->nullable(),

                                    CheckboxList::make('referral_mode')
                                        ->label(__('field.referral_mode'))
                                        ->options(ReferralMode::options())
                                        ->visible(fn (Get $get) => PresentationMode::isValue(
                                            $get('presentation_mode'),
                                            PresentationMode::FORWARDED
                                        ))
                                        ->columns(2)
                                        ->gridDirection('row'),
                                ]),

                        ]),

                    Section::make(__('beneficiary.section.flow.notification'))
                        ->compact()
                        ->columnSpanFull()
                        ->columns(2)
                        ->schema([
                            Select::make('notifier')
                                ->label(__('field.notifier'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(Notifier::options())
                                ->enum(Notifier::class)
                                ->live(),
                            Group::make()
                                ->schema([

                                    Select::make('notification_mode')
                                        ->label(__('field.notification_mode'))
                                        ->placeholder(__('placeholder.select_one'))
                                        ->options(NotificationMode::options())
                                        ->enum(NotificationMode::class),

                                    TextInput::make('notifier_other')
                                        ->label(__('field.notifier_other'))
                                        ->maxLength(100)
                                        ->visible(fn (Get $get) => Notifier::isValue(
                                            $get('notifier'),
                                            Notifier::OTHER
                                        ))]),
                        ]),

                    Section::make(__('beneficiary.section.flow.act_location'))
                        ->compact()
                        ->columnSpanFull()
                        ->columns(1)
                        ->schema([
                            Select::make('act_location')
                                ->label(__('field.act_location'))
                                ->placeholder(__('beneficiary.section.personal_information.placeholders.select_many'))
                                ->options(ActLocation::options())
                                ->rule(new MultipleIn(ActLocation::values()))
                                ->multiple()
                                ->live(),

                            TextInput::make('act_location_other')
                                ->label(__('field.act_location_other'))
                                ->maxLength(100)
                                ->visible(
                                    fn (Get $get) => collect($get('act_location'))
                                        ->filter(fn ($value) => ActLocation::isValue($value, ActLocation::OTHER))
                                        ->isNotEmpty()
                                ),
                        ]),

                    Section::make(__('beneficiary.section.flow.institutions_called'))
                        ->compact()
                        ->columnSpanFull()
                        ->columns(1)
                        ->schema([
                            Select::make('first_called_institution_id')
                                ->label(__('field.first_called_institution'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options($institutionOptions)
                                ->nullable(),

                            Select::make('other_called_institutions')
                                ->label(__('field.other_called_institutions'))
                                ->placeholder(__('beneficiary.section.personal_information.placeholders.select_many'))
                                ->options($institutionOptions)
                                ->multiple()
                                ->nullable(),
                        ]),
                ]),
        ];
    }
}
