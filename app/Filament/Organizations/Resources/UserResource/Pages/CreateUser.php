<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Pages;

use App\Filament\Organizations\Resources\UserResource;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterSave(): void
    {
        /** @var User $user */
        $user = $this->record;
        $roles = $this->data['role_id'] ?? [];

        $pivotData = [];
        foreach ($roles as $roleID) {
            $pivotData[$roleID] = ['organization_id' => Filament::getTenant()->id];
        }

        $user->rolesInOrganization()->sync($pivotData);
    }
}
