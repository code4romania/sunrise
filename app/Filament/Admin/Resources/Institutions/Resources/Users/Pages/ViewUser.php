<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Resources\Users\Pages;

use App\Filament\Admin\Resources\Institutions\Resources\Users\Actions\DeactivateUserAction;
use App\Filament\Admin\Resources\Institutions\Resources\Users\Actions\ReactivateUserAction;
use App\Filament\Admin\Resources\Institutions\Resources\Users\Actions\ResendInvitationAction;
use App\Filament\Admin\Resources\Institutions\Resources\Users\Actions\ResetPasswordAction;
use App\Filament\Admin\Resources\Institutions\Resources\Users\UserResource;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ResendInvitationAction::make(),
            DeactivateUserAction::make(),
            ReactivateUserAction::make(),
            ResetPasswordAction::make(),
        ];
    }
}
