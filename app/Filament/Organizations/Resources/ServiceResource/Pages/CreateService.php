<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\ServiceResource\Pages;

use App\Concerns\PreventMultipleSubmit;
use App\Filament\Organizations\Resources\ServiceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateService extends CreateRecord
{
    use PreventMultipleSubmit;

    protected static string $resource = ServiceResource::class;

    protected static bool $canCreateAnother = false;

    public function getTitle(): string|Htmlable
    {
        return __('service.headings.create_page');
    }

    public function getBreadcrumbs(): array
    {
        return [
            self::getResource()::getUrl() => __('service.headings.navigation'),
            self::getResource()::getUrl('create') => $this->getTitle(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->data['interventions'] = self::$resource::processInterventionsBeforeSave($this->data['interventions']);

        return parent::mutateFormDataBeforeCreate($data);
    }
}
