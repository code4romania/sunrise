<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Filament\Organizations\Resources\Cases\CaseResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListCases extends ListRecords
{
    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('case.headings.list_page');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('case.headings.all_cases');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('register_case')
                ->label(__('case.headings.register_new'))
                ->url(CaseResource::getUrl('create'))
                ->icon('heroicon-m-plus')
                ->button(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
