<?php

declare(strict_types=1);

namespace App\Policies;

use App\Concerns\UserPermissions;
use App\Enums\AdminPermission;
use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    use UserPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->userHasPermission($user, AdminPermission::CAN_CHANGE_ORGANISATION_PROFILE, __FUNCTION__);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Organization $organization): bool
    {
        return $this->userHasPermission($user, AdminPermission::CAN_CHANGE_ORGANISATION_PROFILE, __FUNCTION__);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->userHasPermission($user, AdminPermission::CAN_CHANGE_ORGANISATION_PROFILE, __FUNCTION__);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Organization $organization): bool
    {
        return $this->userHasPermission($user, AdminPermission::CAN_CHANGE_ORGANISATION_PROFILE, __FUNCTION__);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Organization $organization): bool
    {
        return false;

        return $this->userHasPermission($user, AdminPermission::CAN_CHANGE_ORGANISATION_PROFILE, __FUNCTION__);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Organization $organization): bool
    {
        return false;

        return $this->userHasPermission($user, AdminPermission::CAN_CHANGE_ORGANISATION_PROFILE, __FUNCTION__);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Organization $organization): bool
    {
        return false;

        return $this->userHasPermission($user, AdminPermission::CAN_CHANGE_ORGANISATION_PROFILE, __FUNCTION__);
    }
}
