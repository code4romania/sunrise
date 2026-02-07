<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Pages;

use App\Filament\Organizations\Resources\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('user.actions.add_specialist')),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function render(): View
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER,
            fn () => new HtmlString(\sprintf('<span class="px-2 text-sm">%s</span>', __('user.placeholders.table_observations'))),
        );

        return parent::render();
    }
}
