<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Actions;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;

class DeactivateUserAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'deactivate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(function (User $record) {
            if (! $record->hasSetPassword()) {
                return false;
            }

            if ($record->isInactive()) {
                return false;
            }

            if (Filament::auth()->user()->is($record)) {
                return false;
            }

            // TODO: check for permissions
            return Filament::auth()->user()->can('delete', $record);
        });

        $this->label(__('user.actions.deactivate'));

        $this->outlined();

        $this->color('danger');

        $this->icon('heroicon-o-user-minus');

        $this->modalHeading(__('user.action_deactivate_confirm.title'));

        $this->modalWidth('md');

        $this->action(function (User $record) {
            $record->deactivate();
            $this->success();
        });

        $this->successNotificationTitle(__('user.action_deactivate_confirm.success'));
    }
}
