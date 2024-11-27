<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\GeneralStatus;
use App\Filament\Admin\Resources\ResultResource\Pages;
use App\Models\Result;
use App\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->maxWidth('3xl')
                    ->description(__('nomenclature.helper_texts.result'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('nomenclature.labels.result_name'))
                            ->maxLength(200),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading(__('nomenclature.headings.results_table'))
            ->headerActions([
                CreateAction::make()
                    ->label(__('nomenclature.actions.add_result')),
            ])
            ->columns([
                TextColumn::make('name')
                    ->label(__('nomenclature.labels.result_name')),

                TextColumn::make('institutions_count')
                    ->label(__('nomenclature.labels.institutions')),

                TextColumn::make('organizations_count')
                    ->label(__('nomenclature.labels.centers')),

                TextColumn::make('status')
                    ->label(__('nomenclature.labels.status')),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(GeneralStatus::options()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('general.action.change')),
            ])
            ->emptyStateHeading(__('nomenclature.headings.empty_state_results_table'))
            ->emptyStateDescription(null)
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResults::route('/'),
            'create' => Pages\CreateResult::route('/create'),
            'edit' => Pages\EditResult::route('/{record}/edit'),
        ];
    }
}
