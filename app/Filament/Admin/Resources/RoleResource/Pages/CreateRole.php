<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\RoleResource\Pages;

use App\Actions\BackAction;
use App\Concerns\PreventMultipleSubmit;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Admin\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateRole extends CreateRecord
{
    use PreventMultipleSubmit;
    use PreventSubmitFormOnEnter;

    protected static string $resource = RoleResource::class;

    protected static bool $canCreateAnother = false;

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.actions.add_role');
    }

    public function getBreadcrumbs(): array
    {
        return [
            self::$resource::getUrl() => __('nomenclature.titles.list'),
            RoleResource::getUrl('create') => __('nomenclature.actions.add_role'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(RoleResource::getUrl()),
        ];
    }
}
