<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class PasswordReset extends Notification
{
    use Queueable;

    public string $url;

    private mixed $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

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
            ->greeting(__('email.greeting', ['name' => $notifiable->full_name]))
            ->line(__('email.reset_password.intro_line_1'))
            ->subject(__('email.reset_password.subject'))
            ->line(__('email.reset_password.intro_line_2'))
            ->action(__('email.reset_password.action'), $this->url)
            ->line(__('email.reset_password.intro_line_3'))
            ->line(__('email.reset_password.intro_line_4'))
            ->line(__('email.reset_password.intro_line_5'))
            ->line(new HtmlString(__('email.reset_password.intro_line_6')));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
