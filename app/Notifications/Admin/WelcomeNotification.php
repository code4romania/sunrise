<?php

declare(strict_types=1);

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class WelcomeNotification extends Notification
{
    use Queueable;

    public string $organizationRoute = 'filament.organization.auth.welcome';

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
        $institution = $notifiable->institution;

        return (new MailMessage)
            ->greeting(__('email.admin.welcome.greeting', ['name' => $notifiable->first_name]))
            ->subject(__('email.admin.welcome.subject'))
            ->line(
                __('email.admin.welcome.intro_line_1', [
                    'institution_name' => $institution->name,
                    'center_name' => $institution->organizations->first()->name,
                ])
            )
            ->line(__('email.admin.welcome.intro_line_2'))
            ->line(__('email.admin.welcome.intro_line_3'))
            ->action(
                __('email.admin.welcome.accept_invitation'),
                URL::signedRoute($this->organizationRoute, [
                    'tenant' => $institution->organizations->first(),
                    'user' => $notifiable,
                ])
            )
            ->line(__('email.admin.welcome.intro_line_4'))
            ->line(new HtmlString(__('email.organization.welcome.intro_line_5')))
            ->salutation(__('email.salutation'));
    }
}
