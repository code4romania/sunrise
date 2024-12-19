<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MonthlyPlan;
use App\Models\User;

class MonthlyPlanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MonthlyPlan $monthlyPlan): bool
    {
        return $user->hasAccessToBeneficiary($monthlyPlan->beneficiary);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MonthlyPlan $monthlyPlan): bool
    {
        return $user->hasAccessToBeneficiary($monthlyPlan->beneficiary);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MonthlyPlan $monthlyPlan): bool
    {
        return $user->hasAccessToBeneficiary($monthlyPlan->beneficiary);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MonthlyPlan $monthlyPlan): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MonthlyPlan $monthlyPlan): bool
    {
        return true;
    }
}
