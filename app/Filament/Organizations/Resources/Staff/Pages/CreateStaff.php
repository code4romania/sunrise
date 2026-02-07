<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Staff\Pages;

use App\Actions\BackAction;
use App\Concerns\PreventMultipleSubmit;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\Staff\StaffResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CreateStaff extends CreateRecord
{
    use PreventMultipleSubmit;
    use PreventSubmitFormOnEnter;

    protected static string $resource = StaffResource::class;

    protected static bool $canCreateAnother = false;

    public function getTitle(): string|Htmlable
    {
        return __('user.titles.create_specialist');
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(StaffResource::getUrl()),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label(__('user.actions.add_specialist'));
    }

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

    protected function handleRecordCreation(array $data): Model
    {
        $existingUser = User::query()->where('email', $data['email'])->first();

        if ($existingUser) {
            $tenant = Filament::getTenant();
            $existingUser->organizations()->syncWithoutDetaching([$tenant->id]);
            $existingUser->initializeStatus();
            $existingUser->sendWelcomeNotificationInAnotherTenant();

            return $existingUser;
        }

        return parent::handleRecordCreation($data);
    }
}
