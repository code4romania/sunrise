<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\RoleResource\Pages;

use App\Actions\BackAction;
use App\Concerns\PreventSubmitFormOnEnter;
use App\OldFilament\Admin\Actions\ChangeNomenclatureStatusAction;
use App\Filament\Admin\Resources\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditRole extends EditRecord
{
    use PreventSubmitFormOnEnter;

    protected static string $resource = RoleResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->name;
    }

    public function getBreadcrumbs(): array
    {
        return [
            self::$resource::getUrl() => __('nomenclature.titles.list'),
            RoleResource::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->name,
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return self::$resource::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url($this->getRedirectUrl()),

            ChangeNomenclatureStatusAction::make(),

            DeleteAction::make()
                ->label(__('nomenclature.actions.delete_role'))
                ->outlined()
                ->icon('heroicon-o-trash')
                ->disabled(fn () => $this->getRecord()->users->count())
                ->modalHeading(__('nomenclature.actions.delete_role'))
                ->successRedirectUrl(self::$resource::getUrl()),
        ];
    }
}
