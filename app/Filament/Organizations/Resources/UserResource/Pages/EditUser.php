<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Pages;

use App\Filament\Organizations\Resources\UserResource;
use App\Models\Role;
use App\Models\UserRole;
use Filament\Facades\Filament;
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

    protected function afterSave(): void
    {
        $user = $this->record;
//        $roles = $this->data['role_id'] ?? [];

//        $pivotData = [];
//        foreach ($roles as $roleID) {
//            $pivotData[$roleID] = ['organization_id' => Filament::getTenant()->id];
//        }

        foreach ($user->rolesWithoutOrganization as $role) {
            $role->pivot->organization_id = Filament::getTenant()->id;
            $role->pivot->save();
        }
//        dd($user->rolesWithoutOrganization->map(function ($role) $role->pivot->organization_id, $pivotData);}));
//        UserRole::query()
//            ->where('user_id', $user->id)
//            ->wherenull('organization_id')
//            ->update(['organization_id' => Filament::getTenant()->id]);

//        $user->rolesWithoutOrganization()->sync($pivotData);
    }
}
