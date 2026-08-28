<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Services\Pages;

use App\Filament\Admin\Resources\Services\ServiceResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.titles.list').' - '.__('nomenclature.headings.service');
    }
}
