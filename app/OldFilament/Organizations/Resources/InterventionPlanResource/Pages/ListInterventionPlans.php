<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionPlanResource\Pages;

use App\Filament\Organizations\Resources\InterventionPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInterventionPlans extends ListRecords
{
    protected static string $resource = InterventionPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
