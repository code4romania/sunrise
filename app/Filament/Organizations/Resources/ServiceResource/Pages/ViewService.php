<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\ServiceResource\Pages;

use App\Filament\Organizations\Resources\ServiceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewService extends ViewRecord
{
    protected static string $resource = ServiceResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->service->name;
    }

    public function getBreadcrumbs(): array
    {
        return [
            self::getResource()::getUrl() => __('service.headings.navigation'),
            self::getResource()::getUrl('view', ['record' => $this->getRecord()]) => $this->getTitle(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
