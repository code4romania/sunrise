<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Services\Pages;

use App\Filament\Admin\Resources\Services\ServiceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.actions.add_service');
    }

    public function getBreadcrumb(): string
    {
        return __('nomenclature.actions.add_service');
    }
}
