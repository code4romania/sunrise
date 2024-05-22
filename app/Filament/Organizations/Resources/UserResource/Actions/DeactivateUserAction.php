<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Actions;

use App\Enums\UserStatus;
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

        $this->visible(fn (User $record) => UserStatus::isValue($record->status, UserStatus::ACTIVE));

        $this->label(__('user.actions.deactivate'));

        $this->color('danger');

//        $this->icon('heroicon-s-ban');

        $this->modalHeading(__('user.action_deactivate_confirm.title'));

        $this->modalWidth('md');

        $this->action(function (User $record) {
            $record->deactivate();
            $this->success();
        });

        $this->successNotificationTitle(__('user.action_deactivate_confirm.success'));
    }
}
