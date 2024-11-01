<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionServiceResource\Pages;

use App\Filament\Organizations\Resources\InterventionServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterventionServices extends ListRecords
{
    protected static string $resource = InterventionServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
