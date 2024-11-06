<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasUserStatus
{
    public function initializeHasUserStatus()
    {
        $this->casts['deactivated_at'] = 'timestamp';
        $this->fillable[] = 'deactivated_at';
    }

    public function activate(): bool
    {
        return $this->update([
            'deactivated_at' => null,
        ]);
    }

    public function deactivate(): bool
    {
        return $this->update([
            'deactivated_at' => now(),
        ]);
    }

    public function isActive(): bool
    {
        return $this->deactivated_at === null;
    }

    public function isInactive(): bool
    {
        return UserStatus::isValue($this->status, UserStatus::INACTIVE);
    }

    public function setPendingStatus(): void

    {
        return ! $this->isActive();
    }

    public function status(): Attribute
    {
        return Attribute::make(function () {
            if ($this->isInactive()) {
                return UserStatus::INACTIVE;
            }

            if (! $this->hasSetPassword()) {
                return UserStatus::PENDING;
            }

            return UserStatus::ACTIVE;
        });
    }

    public function activate(): void
    {
        $this->update(['status' => UserStatus::ACTIVE]);
    }
}
