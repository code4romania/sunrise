<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Pages;

use App\Filament\Admin\Resources\InstitutionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListInstitutions extends ListRecords
{
    protected static string $resource = InstitutionResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('institution.headings.list_title');
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('institution.actions.create')),
        ];
    }
}
