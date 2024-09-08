<?php

declare(strict_types=1);

namespace App\Policies;

use App\Concerns\UserPermissions;
use App\Enums\AdminPermission;
use App\Models\User;

class UserPolicy
{
    use UserPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->userHasRights($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $this->userHasRights($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->userHasRights($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $this->userHasRights($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return false;

        return $this->userHasRights($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;

        return $this->userHasRights($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;

        return $this->userHasRights($user, __FUNCTION__);
    }

    protected function userHasRights(User $user, ?string $function = null): bool
    {
        if ($this->userIsSuperAdmin($user)) {
            return true;
        }

        if ($this->userIsOrgAdmin($user)) {
            return true;
        }

        if ($function === 'view' &&
            $this->userIsCoordinatorOrChefService($user)) {
            return true;
        }

        if ($this->userIsCoordinatorOrChefService($user) &&
            $this->userHasAdminPermissions($user->admin_permissions, AdminPermission::CAN_CHANGE_STAFF)) {
            return true;
        }

        return false;
    }
}
