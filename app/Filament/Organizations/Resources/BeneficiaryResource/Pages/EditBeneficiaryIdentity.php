<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use alcea\cnp\Cnp;
use App\Concerns\RedirectToIdentity;
use App\Enums\AddressType;
use App\Enums\Citizenship;
use App\Enums\CivilStatus;
use App\Enums\Ethnicity;
use App\Enums\Gender;
use App\Enums\IDType;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Location;
use App\Forms\Components\Select;
use App\Forms\Components\Spacer;
use App\Models\Beneficiary;
use App\Rules\ValidCNP;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class EditBeneficiaryIdentity extends EditRecord
{
    use RedirectToIdentity;

    protected static string $resource = BeneficiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return  __('beneficiary.page.edit_identity.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('view_identity');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.section.identity.tab.beneficiary'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Section::make()
                    ->schema(static::getBeneficiaryIdentityFormSchema()),
            ]);
    }

    public static function getBeneficiaryIdentityFormSchema(?Beneficiary $parentBeneficiary = null): array
    {
        return [
            Grid::make()
                ->maxWidth('3xl')
                ->schema([
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

                    TextInput::make('cnp')
                        ->label(__('field.cnp'))
                        ->placeholder(__('placeholder.cnp'))
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
                        ->afterStateUpdated(function (?string $state, Set $set) {
                            if ($state === null) {
                                return;
                            }

                            if (filled($birthdate = (new Cnp($state))->getBirthDateFromCNP())) {
                                $set('birthdate', $birthdate);
                            }
                        }),

                    Select::make('gender')
                        ->label(__('field.gender'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(Gender::options())
                        ->enum(Gender::class),

                    DatePicker::make('birthdate')
                        ->label(__('field.birthdate'))
                        ->maxDate(today()->endOfDay())
                        ->nullable()
                        ->live(),

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
                            if (! $state || IDType::tryFrom($state)?->is(IDType::NONE)) {
                                $set('id_serial', null);
                                $set('id_number', null);
                            }
                        }),

                    TextInput::make('id_serial')
                        ->label(__('field.id_serial'))
                        ->placeholder(__('placeholder.id_serial'))
                        ->disabled(function (Get $get) {
                            if (! $get('id_type')) {
                                return true;
                            }

                            return IDType::tryFrom($get('id_type'))?->is(IDType::NONE);
                        }),

                    TextInput::make('id_number')
                        ->label(__('field.id_number'))
                        ->placeholder(__('placeholder.id_number'))
                        ->disabled(function (Get $get) {
                            if (! $get('id_type')) {
                                return true;
                            }

                            return IDType::tryFrom($get('id_type'))?->is(IDType::NONE);
                        }),

                    Spacer::make(),

                    Location::make(AddressType::LEGAL_RESIDENCE->value)
                        ->relationship(AddressType::LEGAL_RESIDENCE->value)
                        ->city()
                        ->address()
                        ->environment()
                        ->copyDataInPath(
                            fn (Get $get) => $get('same_as_legal_residence') ?
                                AddressType::EFFECTIVE_RESIDENCE->value :
                                null
                        ),

                    Checkbox::make('same_as_legal_residence')
                        ->label(__('field.same_as_legal_residence'))
                        ->live()
                        ->afterStateUpdated(function (bool $state, Set $set, Get $get) {
                            if ($state) {
                                $set('effective_residence.county_id', $get('legal_residence.county_id'));
                                $set('effective_residence.city_id', $get('legal_residence.city_id'));
                                $set('effective_residence.address', $get('legal_residence.address'));
                                $set('effective_residence.environment', $get('legal_residence.environment'));
                            }
                        })
                        ->columnSpanFull(),

                    Spacer::make(),

                    Location::make(AddressType::EFFECTIVE_RESIDENCE->value)
                        ->relationship(AddressType::EFFECTIVE_RESIDENCE->value)
                        ->city()
                        ->address()
                        ->environment()
                        ->disabled(function (Get $get) {
                            return  $get('same_as_legal_residence');
                        }),

                    Spacer::make(),

                    TextInput::make('primary_phone')
                        ->label(__('field.primary_phone'))
                        ->placeholder(__('placeholder.phone'))
                        ->tel()
                        ->nullable(),

                    TextInput::make('backup_phone')
                        ->label(__('field.backup_phone'))
                        ->placeholder(__('placeholder.phone'))
                        ->tel()
                        ->nullable(),

                    TextInput::make('email')
                        ->label(__('beneficiary.section.identity.labels.email'))
                        ->placeholder(__('beneficiary.placeholder.email'))
                        ->email()
                        ->nullable(),

                    Textarea::make('contact_notes')
                        ->label(__('field.contact_notes'))
                        ->placeholder(__('placeholder.contact_notes'))
                        ->nullable()
                        ->columnSpanFull(),
                ]),
        ];
    }
}
