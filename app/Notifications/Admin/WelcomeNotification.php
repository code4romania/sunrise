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

    public string $organizationRoute = 'filament.admin.auth.welcome';

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
        $organization = $institution->organizations->first();
        $signedUrl = URL::signedRoute($this->organizationRoute, [
            'user' => $notifiable,
        ]);
        $parsed = parse_url(config('app.url'));
        $loginDomain = (is_array($parsed) && isset($parsed['host'])) ? $parsed['host'] : 'www.sunrise.stopviolenteidomestice.ro';

        return (new MailMessage)
            ->greeting(__('email.admin.welcome.greeting', ['name' => $notifiable->first_name]))
            ->subject(__('email.admin.welcome.subject'))
            ->line(
                __('email.admin.welcome.intro_line_1', [
                    'institution_name' => $institution->name,
                    'center_name' => $organization->name,
                ])
            )
            ->line(__('email.admin.welcome.intro_line_2'))
            ->line(__('email.admin.welcome.intro_line_3'))
            ->action(__('email.admin.welcome.accept_invitation'), $signedUrl)
            ->line(__('email.admin.welcome.intro_line_4'))
            ->line(new HtmlString(__('email.admin.welcome.intro_line_5', [
                'login_url' => config('app.url'),
                'login_domain' => $loginDomain,
            ])))
            ->line(__('email.admin.welcome.fallback_url', ['url' => $signedUrl]))
            ->salutation(__('email.salutation'));
    }
}
