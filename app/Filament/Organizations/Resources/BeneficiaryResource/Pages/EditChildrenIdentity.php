<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToIdentity;
use App\Enums\GenderShortValues;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Select;
use App\Forms\Components\TableRepeater;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Awcodes\TableRepeater\Header;
use Carbon\Carbon;
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
use Illuminate\Support\Str;

class EditChildrenIdentity extends EditRecord
{
    use RedirectToIdentity;
    use PreventSubmitFormOnEnter;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return  __('beneficiary.page.edit_children.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('view_identity');
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
            Grid::make()
                ->maxWidth('3xl')
                ->schema([
                    Checkbox::make('doesnt_have_children')
                        ->label(__('field.doesnt_have_children'))
                        ->live()
                        ->columnSpanFull()
                        ->afterStateUpdated(function (bool $state, Set $set, Get $get) {
                            $fields = [
                                'children_total_count',
                                'children_care_count',
                                'children_under_18_care_count',
                                'children_18_care_count',
                                'children_accompanying_count',
                                'children',
                                'children_notes',
                            ];
                            if ($state) {
                                $oldFields = [];
                                foreach ($fields as $field) {
                                    $oldFields[$field] = $get($field);
                                    $set($field, null);
                                }

                                $set('old_children_fields', $oldFields);

                                return;
                            }

                            $oldFields = $get('old_children_fields');
                            if (! $oldFields) {
                                return;
                            }

                            foreach ($oldFields as $field => $value) {
                                $set($field, $value);
                            }
                        }),

                    Hidden::make('old_children_fields'),

                    Grid::make()
                        ->disabled(fn (Get $get) => $get('doesnt_have_children'))
                        ->hidden(fn (Get $get) => $get('doesnt_have_children'))
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

                            TextInput::make('children_under_18_care_count')
                                ->label(__('field.children_under_18_care_count'))
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
                ]),

            TableRepeater::make('children')
                ->reorderable(false)
                ->relationship('children')
                ->columnSpanFull()
                ->extraAttributes(['class' => 'm'])
                ->hiddenLabel()
                ->addActionLabel(__('beneficiary.action.add_child'))
                ->disabled(fn (Get $get) => $get('doesnt_have_children'))
                ->hidden(fn (Get $get) => $get('doesnt_have_children'))
                ->emptyLabel(false)
                ->defaultItems(fn (Get $get) => $get('doesnt_have_children') ? 0 : 1)
                ->headers([
                    Header::make('name')
                        ->label(__('field.child_name')),

                    Header::make('birthdate')
                        ->label(__('field.birthdate')),

                    Header::make('age')
                        ->label(__('field.age'))
                        ->width('60px'),

                    Header::make('gender')
                        ->label(__('field.gender'))
                        ->width('120px'),

                    Header::make('current_address')
                        ->label(__('field.current_address')),

                    Header::make('status')
                        ->label(__('field.child_status')),

                    Header::make('workspace')
                        ->label(__('field.workspace')),
                ])
                ->schema([
                    TextInput::make('name')
                        ->label(__('field.child_name'))
                        ->maxLength(70),

                    DatePicker::make('birthdate')
                        ->label(__('field.birthdate'))
                        ->afterStateUpdated(function (Set $set, $state) {
                            $set('age', rescue(
                                function () use ($state) {
                                    $age = (int) Carbon::createFromFormat('d.m.Y', $state)->diffInYears(today());

                                    return $age < 1 ? '<1' : $age;
                                },
                                rescue: '–',
                                report: false
                            ));
                        })
                        ->live(),

                    TextInput::make('age')
                        ->label(__('field.age'))
                        ->disabled(),

                    Select::make('gender')
                        ->label(__('field.gender'))
                        ->placeholder(__('placeholder.select_gender'))
                        ->options(GenderShortValues::options()),

                    TextInput::make('current_address')
                        ->label(__('field.current_address'))
                        ->maxLength(70),

                    TextInput::make('status')
                        ->label(__('field.child_status'))
                        ->maxLength(70),

                    TextInput::make('workspace')
                        ->label(__('field.workspace'))
                        ->maxLength(70),
                ]),

            Textarea::make('children_notes')
                ->label(__('field.children_notes'))
                ->placeholder(__('placeholder.other_relevant_details'))
                ->disabled(fn (Get $get) => $get('doesnt_have_children'))
                ->hidden(fn (Get $get) => $get('doesnt_have_children'))
                ->maxLength(500)
                ->maxWidth('3xl')
                ->columnSpanFull(),
        ];
    }
}
