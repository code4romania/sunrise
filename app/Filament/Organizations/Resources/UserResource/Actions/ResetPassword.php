<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Actions;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\RateLimiter;

class ResetPassword extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn (User $record) => $record->isActive());

        $this->label(__('user.actions.reset_password'));

        $this->icon('heroicon-o-lock-open');

        $this->modalHeading(__('user.action_reset_password_confirm.title'));

        $this->modalWidth('md');

        $this->action(function (User $record) {
            $key = $this->getRateLimiterKey($record);
            $maxAttempts = 1;

            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                return $this->failure();
            }

            RateLimiter::increment($key, HOUR_IN_SECONDS);

            $record->resetPassword();
//            $record->sendWelcomeNotification();
//            $this->success();
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
