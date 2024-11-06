<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\OrganizationService;
use App\Models\User;

class OrganizationServicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAccessToNomenclature();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrganizationService $organizationService): bool
    {
        return $user->hasAccessToNomenclature();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAccessToNomenclature();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrganizationService $organizationService): bool
    {
        return $user->hasAccessToNomenclature();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrganizationService $organizationService): bool
    {
        return $user->hasAccessToNomenclature();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrganizationService $organizationService): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrganizationService $organizationService): bool
    {
        return false;
    }
}
