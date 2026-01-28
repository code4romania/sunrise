<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceResource\Pages;

use App\Filament\Admin\Resources\ServiceResource;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListServices extends ManageRecords
{
    protected static string $resource = ServiceResource::class;

    protected string $view = 'filament.admin.pages.nomenclature-list';

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.titles.list');
    }
}
