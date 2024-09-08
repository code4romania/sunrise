<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Enums\AdminPermission;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Collection;

trait UserPermissions
{
    protected function userIsSuperAdmin(User $user): ?bool
    {
        return $user->is_admin;
    }

    protected function userIsOrgAdmin(User $user): ?bool
    {
        return $user->is_org_admin;
    }

    protected function userIsCoordinatorOrChefService(User $user): bool
    {
        return $this->userHasRole($user->roles, Role::COORDINATOR) ||
            $this->userHasRole($user->roles, Role::CHEF_SERVICE);
    }

    protected function userHasRole(?Collection $userRoles, Role $role): bool
    {
        return $userRoles?->filter(
            fn (Role $item) => Role::isValue($item, $role)
        )
            ->count() > 0;
    }

    protected function userHasAdminPermissions(?array $userPermissions, AdminPermission $adminPermission): bool
    {
        return ! empty($userPermissions) && \count(
            array_filter(
                $userPermissions,
                fn (string $item) => AdminPermission::tryFrom($item) === $adminPermission
            )
        ) > 0;
    }
}
