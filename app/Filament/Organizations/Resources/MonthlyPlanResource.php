<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\MonthlyPlanResource\Pages;
use App\Models\MonthlyPlan;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MonthlyPlanResource extends Resource
{
    protected static ?string $model = MonthlyPlan::class;

    protected static bool $shouldRegisterNavigation = false;

    public static string $parentResource = InterventionPlanResource::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonthlyPlans::route('/'),
            'create' => Pages\CreateMonthlyPlan::route('/create'),
            'edit' => Pages\EditMonthlyPlanDetails::route('/{record}/edit'),
        ];
    }
}
