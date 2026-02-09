<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Staff\Actions;

use App\Models\User;
use Filament\Actions\Action;

class ReactivateStaffAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'reactivate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn (User $record) => $record->userStatus->isInactive());
        $this->label(__('user.actions.activate'));
        $this->icon('heroicon-o-arrow-path');
        $this->color('success');
        $this->outlined();
        $this->action(function (User $record) {
            $record->activate();
            $this->success();
        });
        $this->successNotificationTitle(__('user.action_reactivate_confirm.success'));
    }
}
