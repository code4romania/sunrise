<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\ServiceResource\Pages;

use App\Filament\Organizations\Resources\ServiceResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string|Htmlable
    {
        return __('service.headings.list_page');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('service.helper_texts.list_page_subheading');
    }
}
