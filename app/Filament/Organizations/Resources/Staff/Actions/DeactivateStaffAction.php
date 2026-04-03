<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Staff\Actions;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;

class DeactivateStaffAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'deactivate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn (User $record): bool => ($record->userStatus?->isActive() ?? false)
            && (Filament::auth()->user()?->is($record) === false));
        $this->label(__('user.actions.deactivate'));
        $this->color('danger');
        $this->icon('heroicon-o-user-minus');
        $this->outlined();
        $this->modalHeading(__('user.action_deactivate_confirm.title'));
        $this->modalDescription(__('user.action_deactivate_confirm.description'));
        $this->modalWidth('md');
        $this->action(function (User $record) {
            $record->deactivate();
            $this->success();
        });
        $this->successNotificationTitle(__('user.action_deactivate_confirm.success'));
    }
}
