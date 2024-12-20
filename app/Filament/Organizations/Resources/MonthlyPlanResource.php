<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\MonthlyPlanResource\Pages;
use App\Models\MonthlyPlan;
use Filament\Resources\Resource;

class MonthlyPlanResource extends Resource
{
    protected static ?string $model = MonthlyPlan::class;

    protected static bool $shouldRegisterNavigation = false;

    public static string $parentResource = InterventionPlanResource::class;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonthlyPlans::route('/'),
            'create' => Pages\CreateMonthlyPlan::route('/create'),
            'edit' => Pages\EditMonthlyPlanDetails::route('/{record}/edit'),
        ];
    }
}
