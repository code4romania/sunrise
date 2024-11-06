<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BeneficiaryIntervention;
use App\Models\User;

class BeneficiaryInterventionPolicy
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
    public function view(User $user, BeneficiaryIntervention $beneficiaryIntervention): bool
    {
        return $user->hasAccessToBeneficiary($beneficiaryIntervention->beneficiary);
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
    public function update(User $user, BeneficiaryIntervention $beneficiaryIntervention): bool
    {
        return $user->hasAccessToBeneficiary($beneficiaryIntervention->beneficiary);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BeneficiaryIntervention $beneficiaryIntervention): bool
    {
        return $user->hasAccessToBeneficiary($beneficiaryIntervention->beneficiary);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BeneficiaryIntervention $beneficiaryIntervention): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BeneficiaryIntervention $beneficiaryIntervention): bool
    {
        return true;
    }
}
