<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Concerns\RedirectToIdentity;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\TableRepeater;
use App\Models\Beneficiary;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditChildrenIdentity extends EditRecord
{
    use RedirectToIdentity;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return  __('beneficiary.page.edit_children.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getIdentityBreadcrumbs();
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.section.identity.tab.children'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Section::make()
                    ->schema(static::getChildrenIdentityFormSchema()),
            ]);
    }

    public static function getChildrenIdentityFormSchema(?Beneficiary $parentBeneficiary = null): array
    {
        return [
            Grid::make()
                ->maxWidth('3xl')
                ->schema([
                    Checkbox::make('doesnt_have_children')
                        ->label(__('field.doesnt_have_children'))
                        ->live()
                        ->columnSpanFull()
                        ->default($parentBeneficiary?->doesnt_have_children)
                        ->afterStateUpdated(function (bool $state, Set $set) {
                            if ($state) {
                                $set('children_total_count', null);
                                $set('children_care_count', null);
                                $set('children_under_10_care_count', null);
                                $set('children_10_18_care_count', null);
                                $set('children_18_care_count', null);
                                $set('children_accompanying_count', null);
                                $set('children', []);
                                $set('children_notes', null);
                            }
                        }),

                    Grid::make()
                        ->disabled(fn (Get $get) => $get('doesnt_have_children'))
                        ->schema([
                            TextInput::make('children_total_count')
                                ->label(__('field.children_total_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(99)
                                ->default($parentBeneficiary?->children_total_count),

                            TextInput::make('children_care_count')
                                ->label(__('field.children_care_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(99)
                                ->default($parentBeneficiary?->children_care_count),

                            TextInput::make('children_under_10_care_count')
                                ->label(__('field.children_under_10_care_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(99)
                                ->default($parentBeneficiary?->children_under_10_care_count),

                            TextInput::make('children_10_18_care_count')
                                ->label(__('field.children_10_18_care_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(99)
                                ->default($parentBeneficiary?->children_10_18_care_count),

                            TextInput::make('children_18_care_count')
                                ->label(__('field.children_18_care_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(99)
                                ->default($parentBeneficiary?->children_18_care_count),

                            TextInput::make('children_accompanying_count')
                                ->label(__('field.children_accompanying_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(99)
                                ->default($parentBeneficiary?->children_accompanying_count),
                        ]),

                    TableRepeater::make('children')
                        ->reorderable(false)
                        ->relationship('children')
                        ->columnSpanFull()
                        ->hiddenLabel()
                        ->hideLabels()
                        ->addActionLabel(__('beneficiary.action.add_child'))
                        ->disabled(fn (Get $get) => $get('doesnt_have_children'))
                        ->emptyLabel(false)
                        ->defaultItems(fn (Get $get) => $get('doesnt_have_children') ? 0 : 1)
                        ->default($parentBeneficiary?->children)
                        ->schema([
                            TextInput::make('name')
                                ->label(__('field.child_name')),

                            TextInput::make('age')
                                ->label(__('field.age')),

                            DatePicker::make('birthdate')
                                ->label(__('field.birthdate')),

                            TextInput::make('current_address')
                                ->label(__('field.current_address')),

                            TextInput::make('status')
                                ->label(__('field.child_status')),
                        ]),

                    Textarea::make('children_notes')
                        ->label(__('field.children_notes'))
                        ->placeholder(__('placeholder.other_relevant_details'))
                        ->disabled(fn (Get $get) => $get('doesnt_have_children'))
                        ->default($parentBeneficiary?->children_notes)
                        ->nullable()
                        ->columnSpanFull(),
                ]),
        ];
    }
}
