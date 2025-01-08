<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Pages;

use App\Actions\BackAction;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditUser extends EditRecord
{
    use PreventSubmitFormOnEnter;

    protected static string $resource = UserResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            self::$resource::getUrl() => self::$resource::getBreadcrumb(),
            self::$resource::getUrl('view', ['record' => $this->record->id]) => $this->record->getFilamentName(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->full_name;
    }

    protected function getRedirectUrl(): string
    {
        return self::$resource::getUrl('view', ['record' => $this->record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url($this->getRedirectUrl()),
        ];
    }
}
