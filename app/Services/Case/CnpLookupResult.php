<?php

declare(strict_types=1);

namespace App\Services\Case;

use App\Models\Beneficiary;

readonly class CnpLookupResult
{
    /**
     * @param  ?Beneficiary  $beneficiaryInUserOtherTenant  Beneficiary with same CNP in another Organization (tenant)
     *                                                      the user belongs to (not the current tenant).
     */
    public function __construct(
        public ?Beneficiary $beneficiaryInTenant,
        public ?Beneficiary $beneficiaryInInstitutionOtherCenter,
        public bool $userHasAccessToTenantBeneficiary,
        public ?Beneficiary $beneficiaryInUserOtherTenant = null,
    ) {}

    public function existsInTenant(): bool
    {
        return $this->beneficiaryInTenant !== null;
    }

    public function existsInInstitutionOtherCenter(): bool
    {
        return $this->beneficiaryInInstitutionOtherCenter !== null;
    }

    public function existsInUserOtherTenant(): bool
    {
        return $this->beneficiaryInUserOtherTenant !== null;
    }

    public function canProceedToRegister(): bool
    {
        if ($this->existsInTenant()) {
            return $this->userHasAccessToTenantBeneficiary;
        }

        return true;
    }

    public function shouldRedirectToView(): bool
    {
        return $this->existsInTenant() && $this->userHasAccessToTenantBeneficiary;
    }

    public function showNoAccessMessage(): bool
    {
        return $this->existsInTenant() && ! $this->userHasAccessToTenantBeneficiary;
    }

    public function canCopyFromOtherCenter(): bool
    {
        return ! $this->existsInTenant()
            && ($this->existsInUserOtherTenant() || $this->existsInInstitutionOtherCenter());
    }

    public function beneficiaryToCopyFrom(): ?Beneficiary
    {
        return $this->beneficiaryInUserOtherTenant ?? $this->beneficiaryInInstitutionOtherCenter;
    }
}
