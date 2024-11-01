<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceResource\Pages;

use App\Filament\Admin\Actions\ChangeNomenclatureStatusAction;
use App\Filament\Admin\Resources\ServiceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            self::$resource::getUrl() => __('nomenclature.titles.list'),
            self::$resource::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->name,
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return self::$resource::getUrl('view', ['record' => $this->getRecord()]);
    }

    public function getTitle(): string|Htmlable
    {
        return  $this->getRecord()->name;
    }

    protected function getActions(): array
    {
        return [
            ChangeNomenclatureStatusAction::make()
                ->relationship('organizationServices'),

            DeleteAction::make()
                ->disabled(fn () => $this->getRecord()->organizationServices()->count() > 0)
                ->successRedirectUrl(self::$resource::getUrl()),
        ];
    }
}
