<?php

declare(strict_types=1);

namespace App\Notifications\Organizations;

use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

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
            ->greeting(__('email.organization.welcome.greeting', ['name' => $notifiable->first_name]))
            ->line(
                __('email.organization.welcome.intro_line_1', [
                    'institution_name' => Filament::getTenant()?->institution->name ?? '',
                    'center_name' => Filament::getTenant()?->name ?? '',
                ])
            )
            ->line(__('email.organization.welcome.intro_line_2'))
            ->line(__('email.organization.welcome.intro_line_3'))
            ->action(
                __('email.organization.welcome.accept_invitation'),
                URL::signedRoute($this->route, ['user' => $notifiable])
            )
            ->line(__('email.organization.welcome.intro_line_4'))
            ->line(new HtmlString(__('email.organization.welcome.intro_line_5')))
            ->salutation(__('email.salutation'));
    }
}
