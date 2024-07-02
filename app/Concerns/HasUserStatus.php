<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Enums\UserStatus;

trait HasUserStatus
{
    public function isActive(): bool
    {
        return UserStatus::isValue($this->status, UserStatus::ACTIVE);
    }

    public function isPending(): bool
    {
        return UserStatus::isValue($this->status, UserStatus::PENDING);
    }

    public function setPendingStatus(): void
    {
        $this->update(['status' => UserStatus::PENDING]);
    }

    public function deactivate(): void
    {
        $this->update(['status' => UserStatus::INACTIVE]);
    }
}
