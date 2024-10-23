<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages\EditBeneficiaryIntervention;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages\ViewBeneficiaryIntervention;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages\ViewMeetings;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages\ViewUnfoldedMeetings;
use App\Filament\Organizations\Resources\InterventionServiceResource\Pages;
use App\Models\InterventionService;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InterventionServiceResource extends Resource
{
    protected static ?string $model = InterventionService::class;

    protected static bool $shouldRegisterNavigation = false;

    public static string $parentResource = InterventionPlanResource::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

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
            'index' => Pages\ListInterventionServices::route('/'),
            'view_intervention' => ViewBeneficiaryIntervention::route('/{parent}/beneficiaryIntervention/{record}'),
            'edit_intervention' => EditBeneficiaryIntervention::route('/{parent}/beneficiaryIntervention/{record}/edit'),
            'view_meetings' => ViewMeetings::route('/{parent}/meetings/{record}'),
            'list_meetings' => ViewUnfoldedMeetings::route('/{parent}/unfoldedMeetings/{record}'),
            //            'create' => Pages\ViewInterventionService::route('/create'),
            //            'edit' => Pages\EditInterventionService::route('/{record}/edit'),
        ];
    }
}
