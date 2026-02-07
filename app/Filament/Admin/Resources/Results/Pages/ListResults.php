<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Results\Pages;

use App\Filament\Admin\Resources\Results\ResultResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListResults extends ListRecords
{
    protected static string $resource = ResultResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.titles.list').' - '.__('nomenclature.headings.results');
    }
}
