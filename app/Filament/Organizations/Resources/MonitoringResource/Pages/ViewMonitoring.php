<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\MonitoringResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMonitoring extends ViewRecord
{
    use HasParentResource;

    protected static string $resource = MonitoringResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\DeleteAction::make(),
        ];
    }
}
