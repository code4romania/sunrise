<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\MonthlyPlanResource\Pages\CreateMonthlyPlan;
use App\Filament\Organizations\Resources\MonthlyPlanResource\Pages\EditMonthlyPlanDetails;
use App\Filament\Organizations\Resources\MonthlyPlanResource\Pages\ListMonthlyPlans;
use App\Models\MonthlyPlan;
use Filament\Resources\Resource;

class MonthlyPlanResource extends Resource
{
    protected static ?string $model = MonthlyPlan::class;

    protected static bool $shouldRegisterNavigation = false;

    public static ?string $parentResource = InterventionPlanResource::class;

    public static function getPages(): array
    {
        return [
            'index' => ListMonthlyPlans::route('/'),
            'create' => CreateMonthlyPlan::route('/create'),
            'edit' => EditMonthlyPlanDetails::route('/{record}/edit'),
        ];
    }
}
