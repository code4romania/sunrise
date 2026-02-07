<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages\ListBeneficiaryInterventions;
use App\Filament\Organizations\Schemas\BeneficiaryInterventionResourceSchema;
use App\Models\BeneficiaryIntervention;
use App\Models\InterventionService;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BeneficiaryInterventionResource extends Resource
{
    protected static ?string $model = BeneficiaryIntervention::class;

    protected static bool $shouldRegisterNavigation = false;

    public static ?string $parentResource = InterventionServiceResource::class;

    public static function form(Schema $schema): Schema
    {
        return BeneficiaryInterventionResourceSchema::form($schema);
    }

    public static function getSchema(?Beneficiary $beneficiary = null, ?int $organizationServiceID = null): array
    {
        return BeneficiaryInterventionResourceSchema::getFormComponents($beneficiary, $organizationServiceID);
    }

    public static function table(Table $table): Table
    {
        return BeneficiaryInterventionResourceSchema::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBeneficiaryInterventions::route('/'),
            //            'create' => Pages\CreateBeneficiaryIntervention::route('/create'),
            //            'edit' => Pages\EditBeneficiaryIntervention::route('/{record}/edit'),
        ];
    }

    public static function getGroupPages(InterventionService $parent, BeneficiaryIntervention $record): array
    {
        $params = ['parent' => $parent, 'record' => $record];

        return [
            __('intervention_plan.headings.intervention_meetings') => InterventionServiceResource::getUrl('view_meetings', $params),
            __('intervention_plan.headings.intervention_indicators') => InterventionServiceResource::getUrl('view_intervention', $params),
            __('intervention_plan.headings.unfolded') => InterventionServiceResource::getUrl('list_meetings', $params),
        ];
    }
}
