<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Actions;

use App\Models\User;
use Filament\Actions\Action;
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

        $this->hidden(fn (User $record) => $record->hasSetPassword());

        $this->label(__('user.actions.resend_invitation'));

        $this->outlined();

        $this->icon('heroicon-o-envelope-open');

        $this->modalHeading(__('user.action_resend_invitation_confirm.title'));

        $this->modalWidth('md');

        $this->action(function (User $record) {
            $key = $this->getRateLimiterKey($record);
            $maxAttempts = 1;

            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $this->failure();

                return;
            }

            RateLimiter::increment($key, HOUR_IN_SECONDS);

            $record->sendWelcomeNotification();
            $this->success();
        });

        $this->successNotificationTitle(__('user.action_resend_invitation_confirm.success'));
        $this->failureNotificationTitle(__('user.action_resend_invitation_confirm.failure'));
    }

    private function getRateLimiterKey(User $user): string
    {
        return 'resend-invitation:' . $user->id;
    }
}
