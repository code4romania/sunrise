<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Concerns\RedirectToPersonalInformation;
use App\Enums\HomeOwnership;
use App\Enums\Income;
use App\Enums\Occupation;
use App\Enums\Studies;
use App\Enums\Ternary;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\Select;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
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
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getPersonalInformationBreadcrumbs();
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
                ]),
        ];
    }
}
