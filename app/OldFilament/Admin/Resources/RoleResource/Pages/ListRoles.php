<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\RoleResource\Pages;

use App\Filament\Admin\Resources\RoleResource;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListRoles extends ManageRecords
{
    protected static string $resource = RoleResource::class;

    protected string $view = 'filament.admin.pages.nomenclature-list';

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.titles.list');
    }
}
