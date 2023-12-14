<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\BeneficiaryResource\Pages;
use App\Models\Beneficiary;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryResource extends Resource
{
    protected static ?string $model = Beneficiary::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $slug = 'cases';

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.beneficiaries._group');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.beneficiaries.cases');
    }

    public static function getModelLabel(): string
    {
        return __('beneficiary.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('beneficiary.label.plural');
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->full_name;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('field.case_id'))
                    ->extraHeaderAttributes([
                        'class' => 'w-1',
                    ]),

                TextColumn::make('full_name')
                    ->label(__('field.beneficiary'))
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label(__('field.open_at'))
                    ->date()
                    ->extraHeaderAttributes([
                        'class' => 'w-1',
                    ]),

                TextColumn::make('last_evaluated_at')
                    ->label(__('field.last_evaluated_at'))
                    ->date()
                    ->extraHeaderAttributes([
                        'class' => 'w-1',
                    ]),

                TextColumn::make('last_serviced_at')
                    ->label(__('field.last_serviced_at'))
                    ->date()
                    ->extraHeaderAttributes([
                        'class' => 'w-1',
                    ]),

                TextColumn::make('status')
                    ->label(__('field.status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label())
                    ->extraHeaderAttributes([
                        'class' => 'w-1',
                    ]),
            ])
            ->filters([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBeneficiaries::route('/'),
            'create' => Pages\CreateBeneficiary::route('/create'),
            'view' => Pages\ViewBeneficiary::route('/{record}'),

            'edit_identity' => Pages\EditBeneficiaryIdentity::route('/{record}/identity'),
        ];
    }
}
