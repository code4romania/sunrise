<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Services\Pages;

use App\Filament\Organizations\Resources\Services\ServiceResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('service.headings.list_page');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return new HtmlString(__('service.helper_texts.list_page_subheading', ['user_manual_url' => '#']));
    }
}
