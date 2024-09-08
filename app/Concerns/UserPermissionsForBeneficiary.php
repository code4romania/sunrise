<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Enums\CasePermission;
use App\Models\Beneficiary;
use App\Models\CaseTeam;
use App\Models\User;

trait UserPermissionsForBeneficiary
{
    use UserPermissions;

    protected function userBelongsToBeneficiaryTeam(User $user, Beneficiary $beneficiary): bool
    {
        return $beneficiary->team
            ->filter(fn (CaseTeam $teamMember) => $teamMember->user_id === $user->id)->count() > 0;
    }

    protected function userHasCasePermissions(?array $userPermissions, CasePermission $casePermission): bool
    {
        return ! empty($userPermissions) && \count(
            array_filter(
                $userPermissions,
                fn (string $item) => CasePermission::tryFrom($item) === $casePermission
            )
        ) > 0;
    }

    protected function userHasAccessToBeneficiary(User $user, ?Beneficiary $beneficiary = null): bool
    {
        if ($this->userIsOrgAdmin($user)) {
            return true;
        }

        if ($this->userIsCoordinatorOrChefService($user)) {
            return true;
        }

        if ($this->userHasCasePermissions($user->case_permissions, CasePermission::HAS_ACCESS_TO_ALL_CASES)) {
            return true;
        }

        if ($beneficiary && $this->userBelongsToBeneficiaryTeam($user, $beneficiary)) {
            return true;
        }

        return false;
    }
}
