<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Actions;

use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\ResetPassword as ResetPasswordNotification;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\Password;

class ResetPasswordAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'reset-password';
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
            return Filament::auth()->user()->can('update', $record);
        });

        $this->label(__('user.actions.reset_password'));

        $this->icon('heroicon-o-lock-open');

        $this->outlined();

        $this->modalHeading(__('user.action_reset_password_confirm.title'));

        $this->modalWidth('md');

        $this->action(function (User $record) {
            $status = Password::broker(Filament::getAuthPasswordBroker())->sendResetLink(['email' => $record->email], function (CanResetPassword $user, string $token): void {
                if (! method_exists($user, 'notify')) {
                    $userClass = $user::class;

                    throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
                }

                $notification = new ResetPasswordNotification($token);
                $notification->url = Filament::getResetPasswordUrl($token, $user);

                $user->notify($notification);
            });

            if ($status !== Password::RESET_LINK_SENT) {
                $this->failureNotificationTitle(__($status));
                $this->failure();

                return;
            }

            $this->successNotificationTitle(__($status));
            $this->success();
        });
    }
}
