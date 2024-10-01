<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BenefitResource\Pages;
use App\Forms\Components\TableRepeater;
use App\Models\Benefit;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BenefitResource extends Resource
{
    protected static ?string $model = Benefit::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('nomenclature.labels.benefit_name'))
                            ->maxWidth('3xl'),
                        TableRepeater::make('benefitTypes')
                            ->relationship('benefitTypes')
                            ->label(__('nomenclature.headings.benefit_types'))
                            ->columnSpanFull()
                            ->hideLabels()
                            ->addActionLabel(__('nomenclature.actions.add_benefit_type'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('nomenclature.labels.benefit_type_name')),
                                Toggle::make('status')
                                    ->default(true)
                                    ->hiddenLabel(),
                            ])
                            ->deleteAction(
                                fn (Action $action) => $action
                                    ->disabled(function (array $arguments, TableRepeater $component): bool {
                                        $items = $component->getState();
                                        $currentItem = $items[$arguments['item']];
                                        // TODO disable if intervention is used

                                        return $currentItem['status'];
                                    })
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBenefits::route('/'),
            'create' => Pages\CreateBenefit::route('/create'),
            'view' => Pages\ViewBenefit::route('/{record}'),
            'edit' => Pages\EditBenefit::route('/{record}/edit'),
        ];
    }
}
