<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionServiceResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Organizations\Resources\InterventionServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterventionServices extends ListRecords
{
    protected static string $resource = InterventionServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
