<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Roles\Pages;

use App\Filament\Admin\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.titles.list').' - '.__('nomenclature.headings.roles').': '.__('nomenclature.actions.add_role');
    }
}
