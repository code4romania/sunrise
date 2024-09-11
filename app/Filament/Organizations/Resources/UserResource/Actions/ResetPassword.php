<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Actions;

use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\ResetPassword as ResetPasswordNotification;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;

class ResetPassword extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'reset-password';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn (User $record) => $record->isActive());

        $this->label(__('user.actions.reset_password'));

        $this->icon('heroicon-o-lock-open');

        $this->outlined();

        $this->modalHeading(__('user.action_reset_password_confirm.title'));

        $this->modalWidth('md');

        $this->action(function (User $record) {
            $key = $this->getRateLimiterKey($record);
            $maxAttempts = 1;

            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $this->failure();
                return;
            }

            RateLimiter::increment($key, HOUR_IN_SECONDS);

            $data = ['email' => $record->email];
            $status = Password::broker(Filament::getAuthPasswordBroker())->sendResetLink(
                $data,
                function (CanResetPassword $user, string $token): void {
                    if (! method_exists($user, 'notify')) {
                        $userClass = $user::class;

                        throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
                    }

                    $notification = new ResetPasswordNotification($token);
                    $notification->url = Filament::getResetPasswordUrl($token, $user);

                    $user->notify($notification);
                },
            );

            if ($status !== Password::RESET_LINK_SENT) {
                Notification::make()
                    ->title(__($status))
                    ->danger()
                    ->send();

                return;
            }

            Notification::make()
                ->title(__($status))
                ->success()
                ->send();
        });

        $this->successNotificationTitle(__('user.action_reset_password_confirm.success'));

        $this->failureNotification(
            fn (Notification $notification) => $notification
                ->danger()
                ->title(__('user.action_reset_password_confirm.failure_title'))
                ->body(__('user.action_reset_password_confirm.failure_body'))
        );
    }

    private function getRateLimiterKey(User $user): string
    {
        return 'reset-password:' . $user->id;
    }
}
