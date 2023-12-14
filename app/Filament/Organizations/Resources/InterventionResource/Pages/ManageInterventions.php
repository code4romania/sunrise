<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionResource\Pages;

use App\Filament\Organizations\Resources\InterventionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageInterventions extends ManageRecords
{
    protected static string $resource = InterventionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('intervention.action.create'))
                ->modalHeading(__('intervention.action.create'))
                ->createAnother(false),
        ];
    }
}
