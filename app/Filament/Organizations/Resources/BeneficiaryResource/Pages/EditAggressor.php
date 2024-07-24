<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Concerns\RedirectToPersonalInformation;
use App\Enums\AggressorLegalHistory;
use App\Enums\AggressorRelationship;
use App\Enums\Citizenship;
use App\Enums\CivilStatus;
use App\Enums\Drug;
use App\Enums\Gender;
use App\Enums\Occupation;
use App\Enums\Studies;
use App\Enums\Ternary;
use App\Enums\Violence;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\Repeater;
use App\Rules\MultipleIn;
use App\Services\Breadcrumb\Beneficiary as BeneficiaryBreadcrumb;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditAggressor extends EditRecord
{
    use RedirectToPersonalInformation;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        // TODO change title after merge #83
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

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.section.personal_information.section.aggressor'));
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
                ->schema(static::aggressorSection()),
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
                ->minItems(1)
                ->deleteAction(
                    fn (Action $action) => $action->label(__('beneficiary.section.personal_information.actions.delete_aggressor'))
                        ->hiddenLabel(false)
                        ->icon(null)
                        ->link()
                        ->color('primary')
                )
                ->itemLabel(function () {
                    static $index = 0;
                    $index++;

                    return __('beneficiary.section.personal_information.heading.aggressor', [
                        'number' => $index,
                    ]);
                })
                ->schema([
                    Select::make('relationship')
                        ->label(__('field.aggressor_relationship'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(AggressorRelationship::options())
                        ->enum(AggressorRelationship::class)
                        ->native(false)
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
                        ->enum(Gender::class)
                        ->native(false),

                    Select::make('citizenship')
                        ->label(__('field.aggressor_citizenship'))
                        ->placeholder(__('placeholder.citizenship'))
                        ->options(Citizenship::options())
                        ->nullable()
                        ->native(false),

                    Select::make('civil_status')
                        ->label(__('field.aggressor_civil_status'))
                        ->placeholder(__('placeholder.civil_status'))
                        ->options(CivilStatus::options())
                        ->enum(CivilStatus::class)
                        ->native(false),

                    Select::make('studies')
                        ->label(__('field.aggressor_studies'))
                        ->placeholder(__('placeholder.studies'))
                        ->options(Studies::options())
                        ->enum(Studies::class)
                        ->native(false),

                    Select::make('occupation')
                        ->label(__('field.aggressor_occupation'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(Occupation::options())
                        ->enum(Occupation::class)
                        ->native(false),

                    Grid::make()
                        ->schema([
                            Select::make('has_violence_history')
                                ->label(__('field.aggressor_has_violence_history'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(Ternary::options())
                                ->enum(Ternary::class)
                                ->native(false)
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
                                ->native(false)
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
                                ->native(false)
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
                                ->native(false)
                                ->live(),

                            TextInput::make('protection_order_notes')
                                ->label(__('field.protection_order_notes'))
                                ->visible(fn (Get $get) => Ternary::isYes($get('has_protection_order'))),
                        ]),
                ]),
        ];
    }
}
