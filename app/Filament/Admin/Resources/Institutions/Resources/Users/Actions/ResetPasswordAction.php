<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Resources\Users\Actions;

use App\Models\Organization;
use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\Password;

class ResetPasswordAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'reset_password';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn (User $record) => static::isUserActive($record)
            && ! Filament::auth()->user()?->is($record));

        $this->label(__('user.actions.reset_password'));

        $this->icon('heroicon-o-lock-open');

        $this->color('gray');

        $this->outlined();

        $this->modalHeading(__('user.action_reset_password_confirm.title'));

        $this->modalSubmitActionLabel(__('user.action_reset_password_confirm.title'));

        $this->modalWidth('md');

        $this->action(function (User $record) {
            $status = Password::broker(Filament::getAuthPasswordBroker())
                ->sendResetLink(
                    ['email' => $record->email],
                    function (CanResetPassword $user, string $token): void {
                        if (! method_exists($user, 'notify')) {
                            $userClass = get_class($user);

                            throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
                        }

                        $notification = new \Filament\Auth\Notifications\ResetPassword($token);
                        $notification->url = Filament::getResetPasswordUrl($token, $user);

                        $user->notify($notification);
                    }
                );

            if ($status !== Password::RESET_LINK_SENT) {
                Notification::make()
                    ->danger()
                    ->title(__('user.action_reset_password_confirm.failure_title'))
                    ->body(__('user.action_reset_password_confirm.failure_body'))
                    ->send();

                return;
            }

            Notification::make()
                ->success()
                ->title(__('user.action_reset_password_confirm.success'))
                ->send();
        });
    }

    public static function isUserActive(User $user): bool
    {
        if (! $user->hasSetPassword()) {
            return false;
        }

        return $user->institution
            ->organizations
            ->contains(
                fn (Organization $organization) => $user->getStatusInOrganization($organization->id)?->isActive()
            );
    }
}
