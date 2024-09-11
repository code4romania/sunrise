<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Actions;

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

        $this->visible(fn (User $record) => $record->isDeactivate());

        $this->label(__('user.actions.reactivate'));

        $this->outlined();

        $this->color('success');

        $this->icon('heroicon-o-arrow-path');

        $this->modalHeading(__('user.action_reactivate_confirm.title'));

        $this->modalWidth('md');

        $this->action(function (User $record) {
            $record->reactivate();
            $this->success();
        });

        $this->successNotificationTitle(__('user.action_reactivate_confirm.success'));
    }
}
