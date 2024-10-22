<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceResource\Pages;

use App\Filament\Admin\Resources\ServiceResource;
use Filament\Resources\Pages\ManageRecords;

class ListServices extends ManageRecords
{
    protected static string $resource = ServiceResource::class;

    protected static string $view = 'filament.admin.pages.nomenclature-list';
}
