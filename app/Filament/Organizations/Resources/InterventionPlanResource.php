<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\InterventionServiceResource\Pages\EditCounselingSheet;
use App\Filament\Organizations\Resources\InterventionServiceResource\Pages\EditInterventionService;
use App\Filament\Organizations\Resources\InterventionServiceResource\Pages\ViewInterventionService;
use App\Models\InterventionPlan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class InterventionPlanResource extends Resource
{
    protected static ?string $model = InterventionPlan::class;

    protected static bool $shouldRegisterNavigation = false;

    public static string $parentResource = BeneficiaryResource::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->relationship('beneficiary')
                    ->schema([
                        TextInput::make('full_name')
                            ->disabled(),
                        TextInput::make('cnp')
                            ->disabled(),
                        TextInput::make('address')
                            ->disabled(),
                    ]),
                Grid::make()
                    ->schema([
                        DatePicker::make('admit_date'),
                        DatePicker::make('plan_date'),
                        DatePicker::make('last_revise_date'),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'view_intervention_service' => ViewInterventionService::route('{parent}/service/{record}'),
            'edit_intervention_service' => EditInterventionService::route('{parent}/service/{record}/edit'),
            'edit_counseling_sheet' => EditCounselingSheet::route('{parent}/service/{record}/editCounselingSheet'),
        ];
    }
}
