<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\InterventionServiceResource\Pages\EditCounselingSheet;
use App\Filament\Organizations\Resources\InterventionServiceResource\Pages\EditInterventionService;
use App\Filament\Organizations\Resources\InterventionServiceResource\Pages\ViewInterventionService;
use App\Filament\Organizations\Resources\MonthlyPlanResource\Pages\CreateMonthlyPlan;
use App\Filament\Organizations\Resources\MonthlyPlanResource\Pages\EditMonthlyPlanDetails;
use App\Filament\Organizations\Resources\MonthlyPlanResource\Pages\EditMonthlyPlanServicesAndInterventions;
use App\Filament\Organizations\Resources\MonthlyPlanResource\Pages\ViewMonthlyPlan;
use App\Models\InterventionPlan;
use Filament\Resources\Resource;

class InterventionPlanResource extends Resource
{
    protected static ?string $model = InterventionPlan::class;

    protected static bool $shouldRegisterNavigation = false;

    public static ?string $parentResource = BeneficiaryResource::class;

    public static function getPages(): array
    {
        return [
            'view_intervention_service' => ViewInterventionService::route('{parent}/service/{record}'),
            'edit_intervention_service' => EditInterventionService::route('{parent}/service/{record}/edit'),
            'edit_counseling_sheet' => EditCounselingSheet::route('{parent}/service/{record}/editCounselingSheet'),

            'create_monthly_plan' => CreateMonthlyPlan::route('{parent}/createMonthlyPlan/{copyLastPlan?}'),
            'view_monthly_plan' => ViewMonthlyPlan::route('{parent}/monthlyPlan{record}'),
            'edit_monthly_plan_details' => EditMonthlyPlanDetails::route('{parent}/monthlyPlan{record}/editDetails'),
            'edit_monthly_plan_services_and_interventions' => EditMonthlyPlanServicesAndInterventions::route('{parent}/monthlyPlan{record}/editServicesAndInterventions'),
        ];
    }
}
