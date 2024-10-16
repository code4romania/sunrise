<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Pages;

use App\Filament\Organizations\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            self::$resource::getUrl() => self::$resource::getBreadcrumb(),
            self::$resource::getUrl('view', ['record' => $this->record->id]) => $this->record->getFilamentName(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return self::$resource::getUrl('view', ['record' => $this->record->id]);
    }
}
