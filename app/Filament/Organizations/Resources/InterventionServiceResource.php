<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages\EditBeneficiaryIntervention;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages\ViewBeneficiaryIntervention;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages\ViewMeetings;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages\ViewUnfoldedMeetings;
use App\Filament\Organizations\Resources\InterventionServiceResource\Pages;
use App\Filament\Organizations\Resources\InterventionServiceResource\Pages\ListInterventionServices;
use App\Models\InterventionService;
use Filament\Resources\Resource;

class InterventionServiceResource extends Resource
{
    protected static ?string $model = InterventionService::class;

    protected static bool $shouldRegisterNavigation = false;

    public static ?string $parentResource = InterventionPlanResource::class;

    public static function getPages(): array
    {
        return [
            'index' => ListInterventionServices::route('/'),
            'view_intervention' => ViewBeneficiaryIntervention::route('/{parent}/beneficiaryIntervention/{record}'),
            'edit_intervention' => EditBeneficiaryIntervention::route('/{parent}/beneficiaryIntervention/{record}/edit'),
            'view_meetings' => ViewMeetings::route('/{parent}/meetings/{record}'),
            'list_meetings' => ViewUnfoldedMeetings::route('/{parent}/unfoldedMeetings/{record}'),
            //            'create' => Pages\ViewInterventionService::route('/create'),
            //            'edit' => Pages\EditInterventionService::route('/{record}/edit'),
        ];
    }
}
