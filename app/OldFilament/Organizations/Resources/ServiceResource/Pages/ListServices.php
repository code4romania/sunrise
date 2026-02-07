<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\ServiceResource\Pages;

use App\Filament\Organizations\Resources\ServiceResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

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
        // TODO add user manual url after implement
        return new HtmlString(__('service.helper_texts.list_page_subheading', ['user_manual_url' => '#']));
    }

    public function getHeader(): ?View
    {
        return view('filament.header-with-subheader-full-width', [
            'heading' => $this->getTitle(),
            'subheading' => $this->getSubheading(),
            'actions' => $this->getHeaderActions(),
            'breadcrumbs' => $this->getBreadcrumbs(),
        ]);
    }
}
