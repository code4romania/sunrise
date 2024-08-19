<?php

declare(strict_types=1);

namespace App\Policies;

use App\Concerns\UserPermissions;
use App\Models\Beneficiary;
use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    use UserPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->userHasAccessToBeneficiary($user, Beneficiary::find(request('parent')));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        return $this->userHasAccessToBeneficiary($user, $document->beneficiary) && $this->documentBelongsToBeneficiary($document);
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
    public function update(User $user, Document $document): bool
    {
        return $this->userHasAccessToBeneficiary($user, $document->beneficiary);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        return $this->userHasAccessToBeneficiary($user, $document->beneficiary);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return false;
    }

    private function documentBelongsToBeneficiary(Document $document): bool
    {
        return (int) $document->beneficiary->id === (int) request('parent');
    }
}
