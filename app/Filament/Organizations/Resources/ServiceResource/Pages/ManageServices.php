<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\ServiceResource\Pages;

use App\Filament\Organizations\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageServices extends ManageRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
