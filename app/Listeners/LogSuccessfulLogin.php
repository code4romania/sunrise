<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        activity('system')
            ->by($event->user)
            ->on($event->user)
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->event('logged_in')
            ->log('logged_in');
    }
}
