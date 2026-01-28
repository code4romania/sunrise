<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use Filament\Schemas\Schema;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToPersonalInformation;
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
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\Repeater;
use App\Forms\Components\Select;
use App\Rules\MultipleIn;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions\Action;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditAggressor extends EditRecord
{
    use RedirectToPersonalInformation;
    use PreventSubmitFormOnEnter;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return  __('beneficiary.page.edit_aggressor.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->record)
            ->getBreadcrumbs('view_personal_information');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.section.personal_information.section.aggressor'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(static::getPersonalInformationFormSchema());
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
            Repeater::make('aggressors')
                ->relationship('aggressors')
                ->maxWidth('3xl')
                ->hiddenLabel()
                ->columns()
                ->minItems(1)
                ->addAction(
                    fn (\Filament\Actions\Action $action): \Filament\Actions\Action => $action
                        ->label(__('beneficiary.section.personal_information.actions.add_aggressor'))
                        ->link()
                        ->color('primary')
                        ->extraAttributes(['class' => 'pull-left'])
                )
                ->deleteAction(
                    fn (\Filament\Actions\Action $action) => $action
                        ->label(__('beneficiary.section.personal_information.actions.delete_aggressor'))
                        ->icon(null)
                        ->link()
                        ->color('danger')
                        ->modalHeading(__('beneficiary.section.personal_information.heading.delete_aggressor'))
                        ->modalDescription(__('beneficiary.section.personal_information.label.delete_aggressor_description'))
                        ->modalSubmitActionLabel(__('general.action.delete'))
                )
                ->itemLabel(function (Get $get) {
                    if (\count($get('aggressors')) <= 1) {
                        return null;
                    }

                    static $index = 0;

                    return __('beneficiary.section.personal_information.heading.aggressor', [
                        'number' => ++$index,
                    ]);
                })
                ->schema([
                    Select::make('relationship')
                        ->label(__('field.aggressor_relationship'))
                        ->placeholder(__('placeholder.select_one'))
                        ->options(AggressorRelationship::options())
                        ->enum(AggressorRelationship::class)
                        ->live(),

                    Textarea::make('relationship_other')
                        ->label(__('field.aggressor_relationship_other'))
                        ->placeholder(__('placeholder.input_text'))
                        ->extraAttributes([
                            'class' => 'h-full',
                        ])
                        ->visible(fn (Get $get) => AggressorRelationship::isValue($get('relationship'), AggressorRelationship::OTHER))
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

                    Grid::make()
                        ->schema([
                            Select::make('has_violence_history')
                                ->label(__('field.aggressor_has_violence_history'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(Ternary::options())
                                ->enum(Ternary::class)
                                ->live(),

                            Group::make([
                                Select::make('violence_types')
                                    ->label(__('field.aggressor_violence_types'))
                                    ->placeholder(__('placeholder.select_many'))
                                    ->options(Violence::options())
                                    ->rule(new MultipleIn(Violence::values()))
                                    ->multiple(),

                            ])->visible(fn (Get $get) => Ternary::isYes($get('has_violence_history')))
                                ->columns(2)
                                ->columnSpanFull(),

                        ]),

                    Grid::make()
                        ->schema([
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
                                ->extraAttributes([
                                    'class' => 'h-full',
                                ])
                                ->maxLength(100),
                        ])->visible(fn (Get $get) => Ternary::isYes($get('has_violence_history'))),

                    Grid::make()
                        ->schema([
                            Select::make('has_protection_order')
                                ->label(__('field.has_protection_order'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(ProtectionOrder::options())
                                ->enum(ProtectionOrder::class)
                                ->live(),

                            Select::make('electronically_monitored')
                                ->label(__('field.electronically_monitored'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(Ternary::options())
                                ->enum(Ternary::class)
                                ->visible(
                                    fn (Get $get) => ProtectionOrder::isValue($get('has_protection_order'), ProtectionOrder::ISSUED_BY_COURT) ||
                                        ProtectionOrder::isValue($get('has_protection_order'), ProtectionOrder::TEMPORARY)
                                ),

                            TextInput::make('protection_order_notes')
                                ->label(__('field.protection_order_notes'))
                                ->visible(
                                    fn (Get $get) => ! ProtectionOrder::isValue($get('has_protection_order'), ProtectionOrder::NO) &&
                                        ! ProtectionOrder::isValue($get('has_protection_order'), ProtectionOrder::UNKNOWN)
                                )
                                ->maxLength(100),
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
                                ->maxLength(100)
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

                ]),
        ];
    }
}
