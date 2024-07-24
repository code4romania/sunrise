<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Concerns\RedirectToIdentity;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\TableRepeater;
use App\Services\Breadcrumb\Beneficiary as BeneficiaryBreadcrumb;
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
        // TODO change title after merge #83
        return  __('beneficiary.page.edit_identity.title', [
            'name' => $this->record->full_name,
            'id' => $this->record->id,
        ]);
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->record)
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

    public static function getChildrenIdentityFormSchema(): array
    {
        return [
            Checkbox::make('doesnt_have_children')
                ->label(__('field.doesnt_have_children'))
                ->live()
                ->columnSpanFull()
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
                        ->maxValue(99),

                    TextInput::make('children_care_count')
                        ->label(__('field.children_care_count'))
                        ->placeholder(__('placeholder.number'))
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(99),

                    TextInput::make('children_under_10_care_count')
                        ->label(__('field.children_under_10_care_count'))
                        ->placeholder(__('placeholder.number'))
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(99),

                    TextInput::make('children_10_18_care_count')
                        ->label(__('field.children_10_18_care_count'))
                        ->placeholder(__('placeholder.number'))
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(99),

                    TextInput::make('children_18_care_count')
                        ->label(__('field.children_18_care_count'))
                        ->placeholder(__('placeholder.number'))
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(99),

                    TextInput::make('children_accompanying_count')
                        ->label(__('field.children_accompanying_count'))
                        ->placeholder(__('placeholder.number'))
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(99),
                ]),

            TableRepeater::make('children')
                ->reorderable(false)
                ->columnSpanFull()
                ->hiddenLabel()
                ->hideLabels()
                ->addActionLabel(__('beneficiary.action.add_child'))
                ->disabled(fn (Get $get) => $get('doesnt_have_children'))
                ->emptyLabel(false)
                ->defaultItems(fn ($get) => $get('doesnt_have_children') ? 0 : 1)
                ->schema([
                    TextInput::make('name')
                        ->label(__('field.child_name')),

                    TextInput::make('age')
                        ->label(__('field.age')),

                    DatePicker::make('birthdate')
                        ->label(__('field.birthdate'))
                        ->native(false),

                    TextInput::make('address')
                        ->label(__('field.current_address')),

                    TextInput::make('status')
                        ->label(__('field.child_status')),
                ]),

            Textarea::make('children_notes')
                ->label(__('field.children_notes'))
                ->placeholder(__('placeholder.other_relevant_details'))
                ->disabled(fn (Get $get) => $get('doesnt_have_children'))
                ->nullable()
                ->columnSpanFull(),
        ];
    }
}
