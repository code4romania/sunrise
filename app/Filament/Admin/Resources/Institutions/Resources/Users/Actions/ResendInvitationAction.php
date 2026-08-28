<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Resources\Users\Actions;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\RateLimiter;

class ResendInvitationAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'resend_invitation';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn (User $record) => ! $record->hasSetPassword());

        $this->label(__('user.actions.resend_invitation'));

        $this->icon('heroicon-o-envelope');

        $this->color('primary');

        $this->action(function (User $record) {
            $key = $this->getRateLimiterKey($record);
            $maxAttempts = 1;

            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                Notification::make()
                    ->danger()
                    ->title(__('user.action_resend_invitation_confirm.failure_title'))
                    ->body(__('user.action_resend_invitation_confirm.failure_body'))
                    ->send();

                return;
            }

            RateLimiter::hit($key, 3600);

            $record->sendWelcomeNotification();

            Notification::make()
                ->success()
                ->title(__('user.action_resend_invitation_confirm.success'))
                ->send();
        });
    }

    private function getRateLimiterKey(User $user): string
    {
        return 'resend-invitation:'.$user->id;
    }
}
