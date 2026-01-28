<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\DetailedEvaluation;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToDetailedEvaluation;
use App\Enums\Occupation;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\Select;
use App\Models\City;
use App\Models\County;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditBeneficiaryPartner extends EditRecord
{
    use PreventSubmitFormOnEnter;
    use RedirectToDetailedEvaluation;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.edit_beneficiary_partner.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('view_detailed_evaluation');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.wizard.partner.label'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components($this->getFormSchema());
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make(__('beneficiary.section.detailed_evaluation.heading.partner'))
                ->relationship('partner')
                ->maxWidth('3xl')
                ->columns()
                ->schema([
                    TextInput::make('last_name')
                        ->label(__('field.last_name'))
                        ->placeholder(__('beneficiary.placeholder.last_name'))
                        ->maxLength(50),

                    TextInput::make('first_name')
                        ->label(__('field.first_name'))
                        ->placeholder(__('beneficiary.placeholder.first_name'))
                        ->maxLength(50),

                    TextInput::make('age')
                        ->label(__('field.age'))
                        ->placeholder(__('beneficiary.placeholder.age'))
                        ->maxLength(2),

                    Select::make('occupation')
                        ->label(__('field.occupation'))
                        ->placeholder(__('beneficiary.placeholder.occupation'))
                        ->options(Occupation::options())
                        ->enum(Occupation::class),

                    Grid::make()
                        ->schema([
                            Select::make('legal_residence.county_id')
                                ->label(__('field.county'))
                                ->placeholder(__('placeholder.county'))
                                ->searchable()
                                ->options(County::pluck('name', 'id')->toArray())
                                ->getSearchResultsUsing(fn (string $search): array => County::query()
                                    ->where('name', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray())
                                ->getOptionLabelUsing(fn ($value) => County::find($value)?->name)
                                ->live()
                                ->afterStateUpdated(function (Set $set, Get $get) {
                                    $set('legal_residence.city_id', null);
                                    if ($get('same_as_legal_residence')) {
                                        $set('effective_residence.county_id', $get('legal_residence.county_id'));
                                        $set('effective_residence.city_id', null);
                                    }
                                }),

                            Select::make('legal_residence.city_id')
                                ->label(__('field.city'))
                                ->placeholder(__('placeholder.city'))
                                ->searchable()
                                ->options([])
                                ->disabled(fn (Get $get) => ! $get('legal_residence.county_id'))
                                ->getSearchResultsUsing(function (string $search, Get $get): array {
                                    if (! $get('legal_residence.county_id')) {
                                        return [];
                                    }

                                    return City::query()
                                        ->where('county_id', (int) $get('legal_residence.county_id'))
                                        ->where('name', 'like', "%{$search}%")
                                        ->limit(50)
                                        ->get()
                                        ->pluck('name_with_uat', 'id')
                                        ->toArray();
                                })
                                ->getOptionLabelUsing(fn ($value) => City::find($value)?->name_with_uat ?? City::find($value)?->name)
                                ->live()
                                ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                    if ($get('same_as_legal_residence')) {
                                        $set('effective_residence.city_id', $state);
                                    }
                                }),

                            TextInput::make('legal_residence.address')
                                ->label(__('field.address'))
                                ->placeholder(__('placeholder.address'))
                                ->maxLength(50)
                                ->lazy()
                                ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                    if ($get('same_as_legal_residence')) {
                                        $set('effective_residence.address', $state);
                                    }
                                }),
                        ])
                        ->columnSpanFull(),

                    Checkbox::make('same_as_legal_residence')
                        ->label(__('field.same_as_legal_residence'))
                        ->live()
                        ->afterStateUpdated(function (bool $state, Set $set, Get $get) {
                            if (! $state) {
                                $set('effective_residence.county_id', null);
                                $set('effective_residence.city_id', null);
                                $set('effective_residence.address', null);
                                $set('effective_residence.environment', null);
                            }

                            if ($state) {
                                $set('effective_residence.county_id', $get('legal_residence.county_id'));
                                $set('effective_residence.city_id', $get('legal_residence.city_id'));
                                $set('effective_residence.address', $get('legal_residence.address'));
                                $set('effective_residence.environment', $get('legal_residence.environment'));
                            }
                        })
                        ->columnSpanFull(),

                    Grid::make()
                        ->schema([
                            Select::make('effective_residence.county_id')
                                ->label(__('field.county'))
                                ->placeholder(__('placeholder.county'))
                                ->searchable()
                                ->options(County::pluck('name', 'id')->toArray())
                                ->getSearchResultsUsing(fn (string $search): array => County::query()
                                    ->where('name', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray())
                                ->getOptionLabelUsing(fn ($value) => County::find($value)?->name)
                                ->live()
                                ->afterStateUpdated(fn (Set $set) => $set('effective_residence.city_id', null))
                                ->disabled(fn (Get $get) => $get('same_as_legal_residence')),

                            Select::make('effective_residence.city_id')
                                ->label(__('field.city'))
                                ->placeholder(__('placeholder.city'))
                                ->searchable()
                                ->options([])
                                ->disabled(fn (Get $get) => $get('same_as_legal_residence') || ! $get('effective_residence.county_id'))
                                ->getSearchResultsUsing(function (string $search, Get $get): array {
                                    if (! $get('effective_residence.county_id')) {
                                        return [];
                                    }

                                    return City::query()
                                        ->where('county_id', (int) $get('effective_residence.county_id'))
                                        ->where('name', 'like', "%{$search}%")
                                        ->limit(50)
                                        ->get()
                                        ->pluck('name_with_uat', 'id')
                                        ->toArray();
                                })
                                ->getOptionLabelUsing(fn ($value) => City::find($value)?->name_with_uat ?? City::find($value)?->name)
                                ->live(),

                            TextInput::make('effective_residence.address')
                                ->label(__('field.address'))
                                ->placeholder(__('placeholder.address'))
                                ->maxLength(50)
                                ->disabled(fn (Get $get) => $get('same_as_legal_residence')),
                        ])
                        ->columnSpanFull(),

                    Textarea::make('observations')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.observations'))
                        ->placeholder(__('beneficiary.placeholder.partner_relevant_observations'))
                        ->maxLength(500),

                ]),
        ];
    }
}
