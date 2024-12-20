<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Concerns\RedirectToPersonalInformation;
use App\Enums\DisabilityDegree;
use App\Enums\DisabilityType;
use App\Enums\Diseases;
use App\Enums\HomeOwnership;
use App\Enums\Income;
use App\Enums\IncomeSource;
use App\Enums\Occupation;
use App\Enums\Studies;
use App\Enums\Ternary;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\Select;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditBeneficiaryPersonalInformation extends EditRecord
{
    use RedirectToPersonalInformation;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return  __('beneficiary.page.edit_personal_information.title', [
            'name' => $this->record->full_name,
            'id' => $this->record->id,
        ]);
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('view_personal_information');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.section.personal_information.section.beneficiary'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(static::getPersonalInformationFormSchema());
    }

    public static function getPersonalInformationFormSchema(): array
    {
        return [
            Section::make()
                ->schema(static::beneficiarySection()),
        ];
    }

    public static function beneficiarySection(): array
    {
        return [
            Grid::make()
                ->maxWidth('3xl')
                ->relationship('details')
                ->schema([
                    Select::make('has_family_doctor')
                        ->label(__('field.has_family_doctor'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(Ternary::options())
                        ->enum(Ternary::class)
                        ->live(),

                    TextInput::make('family_doctor_name')
                        ->label(__('field.family_doctor_name'))
                        ->placeholder(__('placeholder.name'))
                        ->maxLength(80)
                        ->visible(fn (Get $get) => Ternary::isYes($get('has_family_doctor'))),

                    TextInput::make('family_doctor_contact')
                        ->label(__('field.family_doctor_contact'))
                        ->placeholder(__('placeholder.phone_or_email'))
                        ->maxLength(80)
                        ->visible(fn (Get $get) => Ternary::isYes($get('has_family_doctor'))),

                    TextInput::make('family_doctor_address')
                        ->label(__('field.family_doctor_address'))
                        ->placeholder(__('placeholder.address'))
                        ->maxLength(80)
                        ->visible(fn (Get $get) => Ternary::isYes($get('has_family_doctor'))),

                    Grid::make()
                        ->schema([
                            Select::make('health_insurance')
                                ->label(__('beneficiary.section.personal_information.label.health_insurance'))
                                ->options(Ternary::options())
                                ->enum(Ternary::class),

                            Select::make('health_status')
                                ->label(__('beneficiary.section.personal_information.label.health_status'))
                                ->options(Diseases::options())
                                ->live()
                                ->multiple(),

                            Textarea::make('observations_chronic_diseases')
                                ->label(__('beneficiary.section.personal_information.label.observations_chronic_diseases'))
                                ->columnSpanFull()
                                ->visible(fn (Get $get) => \in_array(Diseases::CHRONIC_DISEASES->value, $get('health_status')))
                                ->maxLength(250),

                            Textarea::make('observations_degenerative_diseases')
                                ->label(__('beneficiary.section.personal_information.label.observations_degenerative_diseases'))
                                ->columnSpanFull()
                                ->visible(fn (Get $get) => \in_array(Diseases::DEGENERATIVE_DISEASES->value, $get('health_status')))
                                ->maxLength(250),

                            Textarea::make('observations_mental_illness')
                                ->label(__('beneficiary.section.personal_information.label.observations_mental_illness'))
                                ->columnSpanFull()
                                ->visible(fn (Get $get) => \in_array(Diseases::MENTAL_ILLNESSES->value, $get('health_status')))
                                ->maxLength(250),
                        ]),

                    Grid::make()
                        ->schema([
                            Select::make('psychiatric_history')
                                ->label(__('field.psychiatric_history'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(Ternary::options())
                                ->enum(Ternary::class)
                                ->live(),

                            TextInput::make('psychiatric_history_notes')
                                ->label(__('field.psychiatric_history_notes'))
                                ->placeholder(__('placeholder.observations'))
                                ->maxLength(100)
                                ->visible(fn (Get $get) => Ternary::isYes($get('psychiatric_history'))),

                            Select::make('investigations_for_psychiatric_pathology')
                                ->label(__('intervention_plan.labels.investigations_for_psychiatric_pathology'))
                                ->options(Ternary::options())
                                ->visible(fn (Get $get) => Ternary::isYes($get('psychiatric_history'))),

                            TextInput::make('investigations_observations')
                                ->label(__('intervention_plan.labels.investigations_observations'))
                                ->placeholder(__('placeholder.observations'))
                                ->maxLength(100)
                                ->visible(fn (Get $get) => Ternary::isYes($get('psychiatric_history'))),

                            Select::make('treatment_for_psychiatric_pathology')
                                ->label(__('intervention_plan.labels.treatment_for_psychiatric_pathology'))
                                ->options(Ternary::options())
                                ->visible(fn (Get $get) => Ternary::isYes($get('psychiatric_history'))),

                            TextInput::make('treatment_observations')
                                ->label(__('intervention_plan.labels.treatment_observations'))
                                ->placeholder(__('placeholder.observations'))
                                ->maxLength(100)
                                ->visible(fn (Get $get) => Ternary::isYes($get('psychiatric_history'))),
                        ]),

                    Grid::make()
                        ->schema([
                            Select::make('disabilities')
                                ->label(__('beneficiary.section.personal_information.label.disabilities'))
                                ->options(Ternary::options())
                                ->enum(Ternary::class)
                                ->live(),

                            Select::make('type_of_disability')
                                ->label(__('beneficiary.section.personal_information.label.type_of_disability'))
                                ->options(DisabilityType::options())
                                ->multiple()
                                ->visible(fn (Get $get) => Ternary::isYes($get('disabilities'))),

                            Select::make('degree_of_disability')
                                ->label(__('beneficiary.section.personal_information.label.degree_of_disability'))
                                ->options(DisabilityDegree::options())
                                ->enum(DisabilityDegree::class)
                                ->visible(fn (Get $get) => Ternary::isYes($get('disabilities'))),

                            Textarea::make('observations_disability')
                                ->label(__('beneficiary.section.personal_information.label.observations_disability'))
                                ->placeholder(__('placeholder.observations'))
                                ->maxLength(250)
                                ->visible(fn (Get $get) => Ternary::isYes($get('disabilities'))),
                        ]),

                    Grid::make()
                        ->schema([
                            Select::make('criminal_history')
                                ->label(__('field.criminal_history'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(Ternary::options())
                                ->enum(Ternary::class)
                                ->live(),

                            TextInput::make('criminal_history_notes')
                                ->label(__('field.criminal_history_notes'))
                                ->placeholder(__('placeholder.observations'))
                                ->maxLength(100)
                                ->visible(fn (Get $get) => Ternary::isYes($get('criminal_history'))),
                        ]),

                    Grid::make()
                        ->schema([
                            Select::make('current_contraception')
                                ->label(__('intervention_plan.labels.current_contraception'))
                                ->options(Ternary::options()),

                            TextInput::make('observations_contraception')
                                ->placeholder(__('placeholder.observations'))
                                ->label(__('intervention_plan.labels.observations_contraception'))
                                ->maxLength(100),
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
                        ->maxLength(100)
                        ->columnSpanFull(),

                    TextInput::make('net_income')
                        ->label(__('field.net_income'))
                        ->placeholder(__('placeholder.net_income'))
                        ->mask('9999999999'),

                    Select::make('income')
                        ->label(__('field.income'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(Income::options())
                        ->enum(Income::class),

                    Select::make('income_source')
                        ->label(__('beneficiary.section.personal_information.label.income_source'))
                        ->options(IncomeSource::options())
                        ->multiple(),

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
                ]),
        ];
    }
}
