<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ResultResource\Pages;

use App\Filament\Admin\Resources\ResultResource;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListResults extends ManageRecords
{
    protected static string $resource = ResultResource::class;

    protected string $view = 'filament.admin.pages.nomenclature-list';

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.titles.list');
    }
}
