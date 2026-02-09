<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Staff\Pages;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\Staff\Actions\DeactivateStaffAction;
use App\Filament\Organizations\Resources\Staff\Actions\ReactivateStaffAction;
use App\Filament\Organizations\Resources\Staff\Actions\ResendInvitationAction;
use App\Filament\Organizations\Resources\Staff\Actions\ResetPasswordAction;
use App\Filament\Organizations\Resources\Staff\StaffResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStaff extends ViewRecord
{
    protected static string $resource = StaffResource::class;

    public function getBreadcrumb(): string
    {
        return $this->getTitle();
    }

    public function getTitle(): string
    {
        return $this->record->getFilamentName();
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(StaffResource::getUrl()),

            EditAction::make()
                ->url(StaffResource::getUrl('edit', ['record' => $this->getRecord()])),

            DeactivateStaffAction::make(),

            ResetPasswordAction::make(),

            ResendInvitationAction::make(),

            ReactivateStaffAction::make(),
        ];
    }
}
