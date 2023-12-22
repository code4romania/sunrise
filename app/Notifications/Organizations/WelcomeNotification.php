<?php

declare(strict_types=1);

namespace App\Notifications\Organizations;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class WelcomeNotification extends Notification
{
    use Queueable;

    public string $route = 'filament.organization.auth.welcome';

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('email.organization.welcome.subject'))
            ->line(__('email.organization.welcome.intro_line_1'))
            ->line(__('email.organization.welcome.intro_line_2'))
            ->action(
                __('email.organization.welcome.accept_invitation'),
                URL::signedRoute($this->route, ['user' => $notifiable])
            );
    }
}
