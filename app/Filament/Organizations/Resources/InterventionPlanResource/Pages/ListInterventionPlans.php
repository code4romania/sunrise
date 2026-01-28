<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionPlanResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use Filament\Actions;
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
