<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Enums\ActLocation;
use App\Enums\AggressorLegalHistory;
use App\Enums\AggressorRelationship;
use App\Enums\CivilStatus;
use App\Enums\Drug;
use App\Enums\Gender;
use App\Enums\HomeOwnership;
use App\Enums\Income;
use App\Enums\NotificationMode;
use App\Enums\Notifier;
use App\Enums\Occupation;
use App\Enums\PresentationMode;
use App\Enums\ReferralMode;
use App\Enums\Studies;
use App\Enums\Ternary;
use App\Enums\Violence;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Rules\MultipleIn;
use App\Services\Breadcrumb\Beneficiary as BeneficiaryBreadcrumb;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditBeneficiaryPersonalInformation extends EditRecord
{
    protected static string $resource = BeneficiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return  __('beneficiary.page.edit_personal_information.title', [
            'name' => $this->record->full_name,
            'id' => $this->record->id,
        ]);
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->record)
            ->getPersonalInformationBreadcrumbs();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(static::getPersonalInformationFormSchema());
    }

    public static function getPersonalInformationFormSchema(): array
    {
        return [
            Tabs::make()
                ->columnSpanFull()
                ->maxWidth('3xl')
                ->tabs([
                    Tabs\Tab::make(__('beneficiary.section.personal_information.section.beneficiary'))
                        ->columns()
                        ->schema(static::beneficiarySection()),

                    Tabs\Tab::make(__('beneficiary.section.personal_information.section.aggressor'))
                        ->schema(static::aggressorSection()),

                    Tabs\Tab::make(__('beneficiary.section.personal_information.section.antecedents'))
                        ->columns()
                        ->schema(static::antecedentsSection()),

                    Tabs\Tab::make(__('beneficiary.section.personal_information.section.flow'))
                        ->columns()
                        ->schema(static::flowSection()),
                ]),

        ];
    }

    public static function beneficiarySection(): array
    {
        return [
            Select::make('has_family_doctor')
                ->label(__('field.has_family_doctor'))
                ->placeholder(__('placeholder.select_one'))
                ->options(Ternary::options())
                ->enum(Ternary::class)
                ->live(),

            TextInput::make('family_doctor_name')
                ->label(__('field.family_doctor_name'))
                ->placeholder(__('placeholder.name'))
                ->visible(fn (Get $get) => Ternary::isYes($get('has_family_doctor'))),

            TextInput::make('family_doctor_contact')
                ->label(__('field.family_doctor_contact'))
                ->placeholder(__('placeholder.phone_or_email'))
                ->visible(fn (Get $get) => Ternary::isYes($get('has_family_doctor'))),

            Grid::make()
                ->schema([
                    Select::make('psychiatric_history')
                        ->label(__('field.psychiatric_history'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(Ternary::options())
                        ->enum(Ternary::class)
                        ->live(),

                    TextInput::make('psychiatric_notes')
                        ->label(__('field.psychiatric_notes'))
                        ->visible(fn (Get $get) => Ternary::isYes($get('psychiatric_history'))),
                ]),

            Grid::make()
                ->schema([
                    Select::make('criminal_history')
                        ->label(__('field.criminal_history'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(Ternary::options())
                        ->enum(Ternary::class)
                        ->live(),

                    TextInput::make('criminal_notes')
                        ->label(__('field.criminal_notes'))
                        ->visible(fn (Get $get) => Ternary::isYes($get('criminal_history'))),
                ]),

            Select::make('studies')
                ->label(__('field.studies'))
                ->placeholder(__('placeholder.studies'))
                ->options(Studies::options())
                ->enum(Studies::class),

            Select::make('occupation')
                ->label(__('field.occupation'))
                ->placeholder(__('placeholder.select_one'))
                ->options(Occupation::options())
                ->enum(Occupation::class),

            TextInput::make('workplace')
                ->label(__('field.workplace'))
                ->placeholder(__('placeholder.workplace'))
                ->columnSpanFull(),

            Select::make('income')
                ->label(__('field.income'))
                ->placeholder(__('placeholder.select_one'))
                ->options(Income::options())
                ->enum(Income::class),

            TextInput::make('elder_care_count')
                ->label(__('field.elder_care_count'))
                ->placeholder(__('placeholder.number'))
                ->numeric()
                ->minValue(0)
                ->maxValue(99),

            Select::make('homeownership')
                ->label(__('field.homeownership'))
                ->placeholder(__('placeholder.select_one'))
                ->options(HomeOwnership::options())
                ->enum(HomeOwnership::class),
        ];
    }

    public static function aggressorSection(): array
    {
        return [
            Repeater::make('aggressor')
                ->relationship('aggressor')
                ->columnSpanFull()
                ->hiddenLabel()
                ->columns()
                ->addActionLabel(__('beneficiary.section.personal_information.actions.add_aggressor'))
                ->schema([
                    Select::make('relationship')
                        ->label(__('field.aggressor_relationship'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(AggressorRelationship::options())
                        ->enum(AggressorRelationship::class)
                        ->live(),

                    TextInput::make('age')
                        ->label(__('field.aggressor_age'))
                        ->placeholder(__('placeholder.number'))
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(200),

                    Select::make('gender')
                        ->label(__('field.aggressor_gender'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(Gender::options())
                        ->enum(Gender::class),

                    Select::make('citizenship_id')
                        ->label(__('field.aggressor_citizenship'))
                        ->placeholder(__('placeholder.citizenship'))
                        ->relationship('citizenship', 'name')
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

                    Grid::make()
                        ->schema([
                            Select::make('has_violence_history')
                                ->label(__('field.aggressor_has_violence_history'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(Ternary::options())
                                ->enum(Ternary::class)
                                ->live(),

                            Select::make('violence_types')
                                ->label(__('field.aggressor_violence_types'))
                                ->placeholder(__('placeholder.select_many'))
                                ->visible(fn (Get $get) => Ternary::isYes($get('has_violence_history')))
                                ->options(Violence::options())
                                ->rule(new MultipleIn(Violence::values()))
                                ->multiple(),

                        ]),

                    Grid::make()
                        ->schema([
                            Select::make('has_psychiatric_history')
                                ->label(__('field.aggressor_has_psychiatric_history'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(Ternary::options())
                                ->enum(Ternary::class)
                                ->live(),

                            TextInput::make('psychiatric_history_notes')
                                ->label(__('field.aggressor_psychiatric_history_notes'))
                                ->visible(fn (Get $get) => Ternary::isYes($get('has_psychiatric_history'))),
                        ]),

                    Grid::make()
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
                                ->visible(fn (Get $get) => Ternary::isYes($get('has_drug_history')))
                                ->options(Drug::options())
                                ->rule(new MultipleIn(Drug::values()))
                                ->multiple(),
                        ]),

                    Grid::make()
                        ->schema([
                            Select::make('legal_history')
                                ->label(__('field.aggressor_legal_history'))
                                ->placeholder(__('placeholder.select_many'))
                                ->visible(fn (Get $get) => Ternary::isYes($get('has_violence_history')))
                                ->options(AggressorLegalHistory::options())
                                ->rule(new MultipleIn(AggressorLegalHistory::values()))
                                ->multiple()
                                ->live(),
                        ]),

                    Grid::make()
                        ->schema([
                            Select::make('has_protection_order')
                                ->label(__('field.has_protection_order'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(Ternary::options())
                                ->enum(Ternary::class)
                                ->live(),

                            TextInput::make('protection_order_notes')
                                ->label(__('field.protection_order_notes')),
                        ]),
                ]),
        ];
    }

    public static function antecedentsSection(): array
    {
        return [
            Grid::make()
                ->schema([
                    Select::make('has_police_reports')
                        ->label(__('field.has_police_reports'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(Ternary::options())
                        ->enum(Ternary::class)
                        ->live(),

                    TextInput::make('police_report_count')
                        ->label(__('field.police_report_count'))
                        ->placeholder(__('placeholder.number'))
                        ->visible(fn (Get $get) => Ternary::isYes($get('has_police_reports')))
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(999),
                ]),

            Grid::make()
                ->schema([
                    Select::make('has_medical_reports')
                        ->label(__('field.has_medical_reports'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(Ternary::options())
                        ->enum(Ternary::class)
                        ->live(),

                    TextInput::make('medical_report_count')
                        ->label(__('field.medical_report_count'))
                        ->placeholder(__('placeholder.number'))
                        ->visible(fn (Get $get) => Ternary::isYes($get('has_medical_reports')))
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(999),
                ]),
        ];
    }

    public static function flowSection(): array
    {
        return [
            Grid::make()
                ->schema([
                    Select::make('presentation_mode')
                        ->label(__('field.presentation_mode'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(PresentationMode::options())
                        ->enum(PresentationMode::class)
                        ->live(),

                    Select::make('referring_institution_id')
                        ->label(__('field.referring_institution'))
                        ->placeholder(__('placeholder.select_one'))
                        ->relationship('referringInstitution', 'name')
                        ->visible(fn (Get $get) => PresentationMode::isValue(
                            $get('presentation_mode'),
                            PresentationMode::FORWARDED
                        ))
                        ->nullable(),

                    Select::make('referral_mode')
                        ->label(__('field.referral_mode'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(ReferralMode::options())
                        ->enum(ReferralMode::class)
                        ->visible(fn (Get $get) => PresentationMode::isValue(
                            $get('presentation_mode'),
                            PresentationMode::FORWARDED
                        ))
                        ->nullable(),
                ]),

            Select::make('notifier')
                ->label(__('field.notifier'))
                ->placeholder(__('placeholder.select_one'))
                ->options(Notifier::options())
                ->enum(Notifier::class)
                ->live(),

            Select::make('notification_mode')
                ->label(__('field.notification_mode'))
                ->placeholder(__('placeholder.select_one'))
                ->options(NotificationMode::options())
                ->enum(NotificationMode::class),

            TextInput::make('notifier_other')
                ->label(__('field.notifier_other'))
                ->visible(fn (Get $get) => Notifier::isValue(
                    $get('notifier'),
                    Notifier::OTHER
                )),

            Select::make('act_location')
                ->label(__('field.act_location'))
                ->placeholder(__('placeholder.select_many'))
                ->options(ActLocation::options())
                ->rule(new MultipleIn(ActLocation::values()))
                ->multiple()
                ->live(),

            TextInput::make('act_location_other')
                ->label(__('field.act_location_other'))
                ->visible(
                    fn (Get $get) => collect($get('act_location'))
                        ->filter(fn ($value) => ActLocation::isValue($value, ActLocation::OTHER))
                        ->isNotEmpty()
                ),

            Select::make('first_called_institution_id')
                ->label(__('field.first_called_institution'))
                ->placeholder(__('placeholder.select_one'))
                ->relationship('firstCalledInstitution', 'name')
                ->nullable(),

            Select::make('other_called_institutions')
                ->label(__('field.other_called_institutions'))
                ->placeholder(__('placeholder.select_one'))
                ->relationship('otherCalledInstitution', 'name')
                ->multiple()
                ->nullable(),
        ];
    }
}
