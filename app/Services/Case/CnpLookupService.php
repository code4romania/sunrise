<?php

declare(strict_types=1);

namespace App\Services\Case;

use App\Models\Beneficiary;
use App\Models\Organization;
use App\Models\Scopes\BelongsToCurrentTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CnpLookupService
{
    public function lookup(string $cnp, Organization $tenant, User $user): CnpLookupResult
    {
        $beneficiaryInTenant = Beneficiary::query()
            ->withoutGlobalScope(BelongsToCurrentTenant::class)
            ->forTenant($tenant)
            ->where('cnp', $cnp)
            ->first();

        $userHasAccess = $beneficiaryInTenant !== null
            && $user->hasAccessToBeneficiary($beneficiaryInTenant);

        $beneficiaryInInstitutionOtherCenter = null;
        if ($user->canSearchBeneficiary() && $tenant->institution_id) {
            $beneficiaryInInstitutionOtherCenter = Beneficiary::query()
                ->withoutGlobalScope(BelongsToCurrentTenant::class)
                ->where('cnp', $cnp)
                ->whereHas('organization', fn (Builder $q) => $q->where('institution_id', $tenant->institution_id)->where('id', '!=', $tenant->id))
                ->with('organization')
                ->first();
        }

        $beneficiaryInUserOtherTenant = $this->resolveBeneficiaryInUserOtherTenant($cnp, $tenant, $user);

        return new CnpLookupResult(
            beneficiaryInTenant: $beneficiaryInTenant,
            beneficiaryInInstitutionOtherCenter: $beneficiaryInInstitutionOtherCenter,
            userHasAccessToTenantBeneficiary: $userHasAccess,
            beneficiaryInUserOtherTenant: $beneficiaryInUserOtherTenant,
        );
    }

    /**
     * Same CNP in another center (tenant) where the user is a member — allows pre-filling a new case
     * when registering the person at the current center (no institution-wide search permission required).
     */
    private function resolveBeneficiaryInUserOtherTenant(string $cnp, Organization $tenant, User $user): ?Beneficiary
    {
        $user->loadMissing('organizations');

        $otherOrganizationIds = $user->organizations
            ->where('id', '!=', $tenant->id)
            ->pluck('id');

        if ($otherOrganizationIds->isEmpty()) {
            return null;
        }

        return Beneficiary::query()
            ->withoutGlobalScope(BelongsToCurrentTenant::class)
            ->where('cnp', $cnp)
            ->whereIn('organization_id', $otherOrganizationIds)
            ->first();
    }
}
