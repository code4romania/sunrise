<?php

namespace App\Filament\Admin\Resources\Institutions\Pages;

use App\Filament\Admin\Resources\Institutions\InstitutionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInstitution extends EditRecord
{
    protected static string $resource = InstitutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
