<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\UserInstitutionResource\Actions;

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

        $this->visible(fn (User $record) => $record->isPending());

        $this->label(__('user.actions.resend_invitation'));

        $this->icon('heroicon-o-envelope-open');

        $this->modalHeading(__('user.action_resend_invitation_confirm.title'));

        $this->modalWidth('md');

        $this->action(function (User $record) {
            $key = $this->getRateLimiterKey($record);
            $maxAttempts = 1;

            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                return $this->failure();
            }

            RateLimiter::increment($key, HOUR_IN_SECONDS);

            $record->sendWelcomeNotification();
            $this->success();
        });

        $this->successNotificationTitle(__('user.action_resend_invitation_confirm.success'));

        $this->failureNotification(
            fn (Notification $notification) => $notification
                ->danger()
                ->title(__('user.action_resend_invitation_confirm.failure_title'))
                ->body(__('user.action_resend_invitation_confirm.failure_body'))
        );
    }

    private function getRateLimiterKey(User $user): string
    {
        return 'resend-invitation:' . $user->id;
    }
}
