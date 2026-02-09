<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Staff\Pages;

use App\Actions\BackAction;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\Staff\StaffResource;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditStaff extends EditRecord
{
    use PreventSubmitFormOnEnter;

    protected static string $resource = StaffResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        /** @var User $user */
        $user = $this->getRecord();
        $user->load(['userStatus', 'permissions', 'rolesInOrganization']);

        if (! $user->permissions) {
            $user->permissions()->create([
                'user_id' => $user->id,
                'organization_id' => Filament::getTenant()->id,
            ]);
            $user->load('permissions');
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        $data['role_id'] = $record->rolesInOrganization->pluck('id')->all();

        $tenant = Filament::getTenant();
        $permissions = $record->permissions;
        if ($permissions) {
            $data['permissions'] = [
                'organization_id' => $tenant->id,
                'case_permissions' => $permissions->case_permissions?->map(fn ($p) => $p->value)->all() ?? [],
                'admin_permissions' => $permissions->admin_permissions?->map(fn ($p) => $p->value)->all() ?? [],
            ];
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        $roles = $this->data['role_id'] ?? [];
        $pivotData = [];
        foreach ($roles as $roleID) {
            $pivotData[$roleID] = ['organization_id' => Filament::getTenant()->id];
        }
        $record->rolesInOrganization()->sync($pivotData);
    }

    public function getBreadcrumbs(): array
    {
        return [
            StaffResource::getUrl() => StaffResource::getBreadcrumb(),
            StaffResource::getUrl('view', ['record' => $this->record->id]) => $this->record->getFilamentName(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->getFilamentName();
    }

    protected function getRedirectUrl(): string
    {
        return StaffResource::getUrl('view', ['record' => $this->record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url($this->getRedirectUrl()),
        ];
    }
}
