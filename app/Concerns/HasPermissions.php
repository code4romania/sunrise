<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Enums\AdminPermission;
use App\Enums\CasePermission;
use App\Models\Beneficiary;

trait HasPermissions
{
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function isNgoAdmin(): bool
    {
        return $this->ngo_admin;
    }

    public function hasAccessToAllCases(): bool
    {
        return $this->permissions->case_permissions->contains(CasePermission::HAS_ACCESS_TO_ALL_CASES);
    }

    public function hasAccessToBeneficiary(Beneficiary $beneficiary): bool
    {
        if ($this->isNgoAdmin()) {
            return true;
        }

        if ($this->hasAccessToAllCases()) {
            return true;
        }

        foreach ($beneficiary->specialistsMembers as $user) {
            if ($user->id === $this->id) {
                return true;
            }
        }

        return false;
    }

    public function hasAccessToStaff(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isNgoAdmin()) {
            return true;
        }

        return (bool) $this->permissions?->admin_permissions->contains(AdminPermission::CAN_CHANGE_STAFF);
    }

    public function hasAccessToNomenclature(): bool
    {
        if ($this->isNgoAdmin()) {
            return true;
        }

        return (bool) $this->permissions?->admin_permissions->contains(AdminPermission::CAN_CHANGE_NOMENCLATURE);
    }

    public function hasAccessToCommunity()
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isNgoAdmin()) {
            return true;
        }

        return (bool) $this->permissions?->admin_permissions->contains(AdminPermission::CAN_CHANGE_ORGANISATION_PROFILE);
    }
}
