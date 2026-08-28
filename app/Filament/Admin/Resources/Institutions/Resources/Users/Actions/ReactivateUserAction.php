<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Resources\Users\Actions;

use App\Models\Organization;
use App\Models\User;
use Filament\Actions\Action;

class ReactivateUserAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'reactivate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn (User $record) => static::isUserInactive($record));

        $this->label(__('user.actions.activate'));

        $this->icon('heroicon-o-arrow-path');

        $this->color('primary');

        $this->action(function (User $record) {
            $record->activate();
            $this->success();
        });

        $this->successNotificationTitle(__('user.action_reactivate_confirm.success'));
    }

    public static function isUserInactive(User $user): bool
    {
        return $user->institution
            ->organizations
            ->contains(
                fn (Organization $organization) => $user->getStatusInOrganization($organization->id)?->isInactive()
            );
    }
}
