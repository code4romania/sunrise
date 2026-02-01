<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Resources\Users\Actions;

use App\Models\Organization;
use App\Models\User;
use Filament\Actions\Action;

class DeactivateUserAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'deactivate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn (User $record) => ResetPasswordAction::isUserActive($record));

        $this->label(__('user.actions.deactivate'));

        $this->color('danger');

        $this->outlined();

        $this->icon('heroicon-o-user-minus');

        $this->modalHeading(__('user.action_deactivate_confirm.title'));

        $this->modalDescription(__('user.action_deactivate_confirm.description'));

        $this->modalWidth('md');

        $this->action(function (User $record) {
            $record->institution
                ->organizations
                ->each(
                    fn (Organization $organization) => $record->getStatusInOrganization($organization->id)?->deactivate()
                );
            $this->success();
        });

        $this->successNotificationTitle(__('user.action_deactivate_confirm.success'));
    }
}
