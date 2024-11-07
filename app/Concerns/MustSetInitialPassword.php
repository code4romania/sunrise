<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Notifications\Admin\WelcomeNotification as AdminWelcomeNotification;
use App\Notifications\Organizations\WelcomeNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait MustSetInitialPassword
{
    protected static function bootMustSetInitialPassword(): void
    {
        static::creating(function (self $user) {
            if (! $user->password) {
                $user->password = Hash::make(Str::random(128));
            }
        });

        static::created(function (self $user) {
            $user->sendWelcomeNotification();
        });
    }

    public function hasSetPassword(): bool
    {
        return ! \is_null($this->password_set_at);
    }

    public function setPassword(string $password): bool
    {
        return $this->update([
            'password' => Hash::make($password),
            'password_set_at' => $this->freshTimestamp(),
        ]);
    }

    public function sendWelcomeNotification(): void
    {
        $this->notify(
            $this->ngo_admin
                ? new AdminWelcomeNotification
                : new WelcomeNotification
        );
    }
}
