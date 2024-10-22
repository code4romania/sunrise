<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\ServiceResource\Pages;

use App\Filament\Organizations\Resources\ServiceResource;
use App\Filament\Organizations\Resources\ServiceResource\Actions\ChangeStatusAction;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getRedirectUrl(): ?string
    {
        return self::getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    public function getTitle(): string|Htmlable
    {
        return __('service.headings.edit_page', ['name' => $this->getRecord()->service?->name]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            self::getResource()::getUrl() => __('service.headings.navigation'),
            self::getResource()::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->service?->name,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            ChangeStatusAction::make(),

            Actions\DeleteAction::make()
                ->label(__('service.actions.delete'))
                ->icon('heroicon-s-trash')
                ->outlined(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->data['interventions'] = self::$resource::processInterventionsBeforeSave($this->data['interventions']);
    }
}