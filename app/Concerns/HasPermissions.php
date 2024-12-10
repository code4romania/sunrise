<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Enums\AdminPermission;
use App\Enums\CasePermission;
use App\Models\Beneficiary;
use Filament\Facades\Filament;

trait HasPermissions
{
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function isNgoAdmin(): bool
    {
        if (! $this->ngo_admin) {
            return false;
        }

        if (! Filament::getTenant()) {
            return true;
        }

        return $this->institution_id === Filament::getTenant()->institution_id;
    }

    public function hasAccessToAllCases(): bool
    {
        return $this->permissions?->case_permissions->contains(CasePermission::HAS_ACCESS_TO_ALL_CASES);
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

    public function hasAccessToCommunity(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isNgoAdmin()) {
            return true;
        }

        return (bool) $this->permissions?->admin_permissions->contains(AdminPermission::CAN_CHANGE_ORGANISATION_PROFILE);
    }

    public function canSearchBeneficiary(): bool
    {
        if ($this->isNgoAdmin()) {
            return true;
        }

        return $this->permissions?->case_permissions->contains(CasePermission::CAN_SEARCH_AND_COPY_CASES_IN_ALL_CENTERS);
    }

    public function hasAccessToReports(): bool
    {
        if ($this->isNgoAdmin()) {
            return true;
        }

        return (bool) $this->permissions?->case_permissions->contains(CasePermission::HAS_ACCESS_TO_STATISTICS);
    }
}
