<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Resources\UserInstitutionResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\InstitutionResource\Resources\UserInstitutionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserInstitutions extends ListRecords
{
    protected static string $resource = UserInstitutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
