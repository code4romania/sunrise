<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Schemas;

use alcea\cnp\Cnp;
use App\Enums\Citizenship;
use App\Enums\CivilStatus;
use App\Enums\Ethnicity;
use App\Enums\Gender;
use App\Enums\IDType;
use App\Enums\ResidenceEnvironment;
use App\Forms\Components\CountyCitySelect;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Select;
use App\Forms\Components\Spacer;
use App\Models\Beneficiary;
use App\Models\Country;
use App\Rules\ValidCNP;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rules\Unique;

class BeneficiaryIdentityFormSchema
{
    private const OTHER_COUNTRY_OPTION = '__other_country__';

    /**
     * @return array<int, mixed>
     */
    public static function getSchema(?Beneficiary $parentBeneficiary = null, bool $withCnpField = true): array
    {
        $firstPartOfSchema = [
            Hidden::make('initial_id'),

            TextInput::make('last_name')
                ->label(__('field.last_name'))
                ->placeholder(__('placeholder.last_name'))
                ->maxLength(50)
                ->required(),

            TextInput::make('first_name')
                ->label(__('field.first_name'))
                ->placeholder(__('placeholder.first_name'))
                ->maxLength(50)
                ->required(),

            TextInput::make('prior_name')
                ->label(__('field.prior_name'))
                ->placeholder(__('placeholder.prior_name'))
                ->maxLength(50)
                ->nullable(),

            Select::make('civil_status')
                ->label(__('field.civil_status'))
                ->placeholder(__('placeholder.civil_status'))
                ->options(CivilStatus::options())
                ->enum(CivilStatus::class),
        ];

        if ($withCnpField) {
            $firstPartOfSchema[] = TextInput::make('cnp')
                ->label(__('field.cnp'))
                ->placeholder(__('placeholder.cnp'))
                ->maxLength(13)
                ->mask('9999999999999')
                ->unique(
                    ignorable: $parentBeneficiary,
                    ignoreRecord: true,
                    modifyRuleUsing: function (Unique $rule, ?Beneficiary $record) use ($parentBeneficiary) {
                        $initialID = 0;
                        if ($parentBeneficiary?->id) {
                            $initialID = $parentBeneficiary->initial_id ?? $parentBeneficiary->id;
                        }
                        if (! $initialID && $record) {
                            $initialID = $record->initial_id ?? $record->id;
                        }

                        return
                            $rule->where(fn (Builder $query) => $query->whereNot('id', $initialID)
                                ->where(fn (Builder $query) => $query->whereNot('initial_id', $initialID)
                                    ->orWhereNull('initial_id')));
                    }
                )
                ->nullable()
                ->rule(new ValidCNP)
                ->lazy()
                ->afterStateHydrated(function (?string $state, Set $set, Get $get): void {
                    if ($state === null || $state === '') {
                        return;
                    }

                    if (filled($get('birthdate'))) {
                        return;
                    }

                    if (filled($birthdate = (new Cnp($state))->getBirthDateFromCNP('Y-m-d'))) {
                        $set('birthdate', $birthdate);
                    }
                })
                ->afterStateUpdated(function (?string $state, Set $set) {
                    if ($state === null) {
                        return;
                    }

                    if (filled($birthdate = (new Cnp($state))->getBirthDateFromCNP('Y-m-d'))) {
                        $set('birthdate', $birthdate);
                    }
                });
        }

        return [
            Grid::make()
                ->maxWidth('3xl')
                ->schema([
                    ...$firstPartOfSchema,

                    Select::make('gender')
                        ->label(__('field.gender'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(Gender::options())
                        ->enum(Gender::class),

                    DatePicker::make('birthdate')
                        ->label(__('field.birthdate'))
                        ->format('Y-m-d')
                        ->live(),

                    Placeholder::make('beneficiary_age_display')
                        ->label(__('field.age'))
                        ->content(function (Get $get): string {
                            $bd = $get('birthdate');
                            if ($bd === null || $bd === '') {
                                return '—';
                            }
                            try {
                                $date = $bd instanceof \Carbon\CarbonInterface ? $bd : Carbon::parse($bd);
                            } catch (\Throwable) {
                                return '—';
                            }

                            return trans_choice('general.age', $date->age);
                        }),

                    TextInput::make('birthplace')
                        ->label(__('field.birthplace'))
                        ->placeholder(__('placeholder.birthplace'))
                        ->maxLength(50)
                        ->nullable(),

                    Spacer::make(),

                    Select::make('citizenship')
                        ->label(__('field.citizenship'))
                        ->placeholder(__('placeholder.citizenship'))
                        ->options(Citizenship::options())
                        ->nullable(),

                    Select::make('ethnicity')
                        ->label(__('field.ethnicity'))
                        ->placeholder(__('placeholder.ethnicity'))
                        ->options(Ethnicity::options())
                        ->nullable(),

                    Select::make('id_type')
                        ->label(__('field.id_type'))
                        ->placeholder(__('placeholder.id_type'))
                        ->options(IDType::options())
                        ->enum(IDType::class)
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if (! $state || $state->is(IDType::NONE)) {
                                $set('id_serial', null);
                                $set('id_number', null);
                            }
                        }),

                    TextInput::make('id_serial')
                        ->label(__('field.id_serial'))
                        ->placeholder(__('placeholder.id_serial'))
                        ->maxLength(2)
                        ->disabled(function (Get $get) {
                            if (! $get('id_type')) {
                                return true;
                            }

                            return $get('id_type')?->is(IDType::NONE);
                        }),

                    TextInput::make('id_number')
                        ->label(__('field.id_number'))
                        ->placeholder(__('placeholder.id_number'))
                        ->maxLength(9)
                        ->mask('999999999')
                        ->numeric()
                        ->disabled(function (Get $get) {
                            if (! $get('id_type')) {
                                return true;
                            }

                            return $get('id_type')?->is(IDType::NONE);
                        }),

                    Spacer::make(),

                    Section::make(__('field.legal_residence'))
                        ->compact()
                        ->schema([
                            Grid::make()
                                ->schema([
                                    Select::make('legal_residence.country_id')
                                        ->label(__('field.country'))
                                        ->options(fn (): array => self::getCountryOptions())
                                        ->default(fn (): int|string|null => self::getRomaniaCountryId())
                                        ->live()
                                        ->dehydrateStateUsing(fn ($state): mixed => self::normalizeCountryStateForSave($state))
                                        ->afterStateHydrated(function (Set $set, $state): void {
                                            if (blank($state)) {
                                                $set('legal_residence.country_id', self::getRomaniaCountryId());

                                                return;
                                            }
                                            if (! self::isRomaniaSelected($state)) {
                                                $set('legal_residence.country_id', self::OTHER_COUNTRY_OPTION);
                                            }
                                        })
                                        ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                                            if (! self::isRomaniaSelected($state)) {
                                                $set('legal_residence.county_id', null);
                                                $set('legal_residence.city_id', null);
                                            }

                                            if ($get('same_as_legal_residence')) {
                                                $set('effective_residence.country_id', $state);
                                                if (! self::isRomaniaSelected($state)) {
                                                    $set('effective_residence.county_id', null);
                                                    $set('effective_residence.city_id', null);
                                                }
                                            }
                                        }),

                                    ...CountyCitySelect::make()
                                        ->countyField('legal_residence.county_id')
                                        ->cityField('legal_residence.city_id')
                                        ->countyLabel(__('field.county'))
                                        ->cityLabel(__('field.city'))
                                        ->countyPlaceholder(__('placeholder.county'))
                                        ->cityPlaceholder(__('placeholder.city'))
                                        ->required()
                                        ->countyDisabled(fn (Get $get): bool => ! self::isRomaniaSelected($get('legal_residence.country_id')))
                                        ->cityDisabled(fn (Get $get): bool => ! self::isRomaniaSelected($get('legal_residence.country_id')) || ! $get('legal_residence.county_id'))
                                        ->countyAfterStateUpdated(function (Set $set, Get $get): void {
                                            if ($get('same_as_legal_residence')) {
                                                $set('effective_residence.county_id', $get('legal_residence.county_id'));
                                                $set('effective_residence.city_id', null);
                                            }
                                        })
                                        ->cityAfterStateUpdated(function (Set $set, Get $get, $state): void {
                                            if ($get('same_as_legal_residence')) {
                                                $set('effective_residence.city_id', $state);
                                            }
                                        })
                                        ->schema(),

                                    TextInput::make('legal_residence.address')
                                        ->label(__('field.address'))
                                        ->placeholder(__('placeholder.address'))
                                        ->maxLength(100)
                                        ->lazy()
                                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                            if ($get('same_as_legal_residence')) {
                                                $set('effective_residence.address', $state);
                                            }
                                        }),

                                    Select::make('legal_residence.environment')
                                        ->label(__('field.environment'))
                                        ->placeholder(__('placeholder.residence_environment'))
                                        ->options(ResidenceEnvironment::options())
                                        ->enum(ResidenceEnvironment::class)
                                        ->lazy()
                                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                            if ($get('same_as_legal_residence')) {
                                                $set('effective_residence.environment', $state);
                                            }
                                        }),
                                ])
                                ->columnSpanFull(),
                        ])
                        ->columnSpanFull(),

                    Checkbox::make('same_as_legal_residence')
                        ->label(__('field.same_as_legal_residence'))
                        ->live()
                        ->afterStateUpdated(function (bool $state, Set $set, Get $get) {
                            if (! $state) {
                                $set('effective_residence.country_id', self::getRomaniaCountryId());
                                $set('effective_residence.county_id', null);
                                $set('effective_residence.city_id', null);
                                $set('effective_residence.address', null);
                                $set('effective_residence.environment', null);
                            }

                            if ($state) {
                                $set('effective_residence.country_id', $get('legal_residence.country_id'));
                                $set('effective_residence.county_id', $get('legal_residence.county_id'));
                                $set('effective_residence.city_id', $get('legal_residence.city_id'));
                                $set('effective_residence.address', $get('legal_residence.address'));
                                $set('effective_residence.environment', $get('legal_residence.environment'));
                            }
                        })
                        ->columnSpanFull(),

                    Spacer::make(),

                    Section::make(__('field.effective_residence'))
                        ->compact()
                        ->schema([
                            Grid::make()
                                ->schema([
                                    Select::make('effective_residence.country_id')
                                        ->label(__('field.country'))
                                        ->options(fn (): array => self::getCountryOptions())
                                        ->default(fn (): int|string|null => self::getRomaniaCountryId())
                                        ->live()
                                        ->dehydrateStateUsing(fn ($state): mixed => self::normalizeCountryStateForSave($state))
                                        ->disabled(fn (Get $get): bool => (bool) $get('same_as_legal_residence'))
                                        ->afterStateHydrated(function (Set $set, Get $get, $state): void {
                                            if ((bool) $get('same_as_legal_residence')) {
                                                $set('effective_residence.country_id', $get('legal_residence.country_id'));

                                                return;
                                            }
                                            if (blank($state)) {
                                                $set('effective_residence.country_id', self::getRomaniaCountryId());

                                                return;
                                            }
                                            if (! self::isRomaniaSelected($state)) {
                                                $set('effective_residence.country_id', self::OTHER_COUNTRY_OPTION);
                                            }
                                        })
                                        ->afterStateUpdated(function (Set $set, $state): void {
                                            if (! self::isRomaniaSelected($state)) {
                                                $set('effective_residence.county_id', null);
                                                $set('effective_residence.city_id', null);
                                            }
                                        }),

                                    ...CountyCitySelect::make()
                                        ->countyField('effective_residence.county_id')
                                        ->cityField('effective_residence.city_id')
                                        ->countyLabel(__('field.county'))
                                        ->cityLabel(__('field.city'))
                                        ->countyPlaceholder(__('placeholder.county'))
                                        ->cityPlaceholder(__('placeholder.city'))
                                        ->required()
                                        ->countyDisabled(fn (Get $get): bool => $get('same_as_legal_residence') || ! self::isRomaniaSelected($get('effective_residence.country_id')))
                                        ->cityDisabled(fn (Get $get): bool => $get('same_as_legal_residence') || ! self::isRomaniaSelected($get('effective_residence.country_id')) || ! $get('effective_residence.county_id'))
                                        ->schema(),

                                    TextInput::make('effective_residence.address')
                                        ->label(__('field.address'))
                                        ->placeholder(__('placeholder.address'))
                                        ->maxLength(100)
                                        ->disabled(fn (Get $get) => $get('same_as_legal_residence')),

                                    Select::make('effective_residence.environment')
                                        ->label(__('field.environment'))
                                        ->placeholder(__('placeholder.residence_environment'))
                                        ->options(ResidenceEnvironment::options())
                                        ->enum(ResidenceEnvironment::class)
                                        ->disabled(fn (Get $get) => $get('same_as_legal_residence')),
                                ])
                                ->columnSpanFull(),
                        ])
                        ->columnSpanFull(),

                    Spacer::make(),

                    TextInput::make('primary_phone')
                        ->label(__('field.primary_phone'))
                        ->placeholder(__('placeholder.phone'))
                        ->maxLength(14)
                        ->tel()
                        ->nullable(),

                    TextInput::make('backup_phone')
                        ->label(__('field.backup_phone'))
                        ->placeholder(__('placeholder.phone'))
                        ->maxLength(14)
                        ->tel()
                        ->nullable(),

                    TextInput::make('email')
                        ->label(__('beneficiary.section.identity.labels.email'))
                        ->placeholder(__('beneficiary.placeholder.email'))
                        ->maxLength(50)
                        ->email()
                        ->nullable(),

                    TextInput::make('social_media')
                        ->label(__('beneficiary.section.identity.labels.social_media'))
                        ->placeholder(__('beneficiary.placeholder.social_media'))
                        ->maxLength(300)
                        ->nullable(),

                    TextInput::make('contact_person_name')
                        ->label(__('beneficiary.section.identity.labels.contact_person_name'))
                        ->placeholder(__('beneficiary.placeholder.contact_person_name'))
                        ->maxLength(50)
                        ->nullable(),

                    TextInput::make('contact_person_phone')
                        ->label(__('beneficiary.section.identity.labels.contact_person_phone'))
                        ->placeholder(__('beneficiary.placeholder.contact_person_phone'))
                        ->tel()
                        ->maxLength(14)
                        ->nullable(),
                ]),
        ];
    }

    private static function getRomaniaCountryId(): ?int
    {
        return Country::query()
            ->whereRaw('LOWER(name) = ?', ['romania'])
            ->value('id');
    }

    /**
     * @return array<int|string, string>
     */
    private static function getCountryOptions(): array
    {
        $romaniaId = self::getRomaniaCountryId();
        if ($romaniaId === null) {
            return [self::OTHER_COUNTRY_OPTION => __('beneficiary.section.identity.labels.country_other')];
        }

        return [
            $romaniaId => __('beneficiary.section.identity.labels.country_romania'),
            self::OTHER_COUNTRY_OPTION => __('beneficiary.section.identity.labels.country_other'),
        ];
    }

    private static function isRomaniaSelected(mixed $state): bool
    {
        $romaniaId = self::getRomaniaCountryId();

        return $romaniaId !== null && (string) $state === (string) $romaniaId;
    }

    private static function normalizeCountryStateForSave(mixed $state): ?int
    {
        return self::isRomaniaSelected($state) ? (int) $state : null;
    }
}
