<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Concerns;

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Models\Beneficiary;
use Illuminate\Contracts\View\View;

trait InteractsWithBeneficiaryDetailsPanel
{
    public bool $isBeneficiaryDetailsPanelOpen = false;

    public function openBeneficiaryDetailsPanel(): void
    {
        $this->isBeneficiaryDetailsPanelOpen = true;
    }

    public function closeBeneficiaryDetailsPanel(): void
    {
        $this->isBeneficiaryDetailsPanelOpen = false;
    }

    public function getFooter(): ?View
    {
        $beneficiary = $this->getBeneficiaryForDetailsPanel();
        if ($beneficiary === null) {
            return parent::getFooter();
        }

        return view('filament.organizations.components.beneficiary-details-fab-and-panel', [
            'beneficiary' => $beneficiary,
            'panelOpen' => $this->isBeneficiaryDetailsPanelOpen,
            'viewCaseUrl' => CaseResource::getUrl('view', ['record' => $beneficiary]),
        ]);
    }

    protected function getBeneficiaryForDetailsPanel(): ?Beneficiary
    {
        if (property_exists($this, 'beneficiary') && $this->beneficiary instanceof Beneficiary) {
            return $this->beneficiary;
        }

        $record = $this->getRecord();
        if ($record instanceof Beneficiary) {
            return $record;
        }

        if (method_exists($this, 'getParentRecord')) {
            $parent = $this->getParentRecord();
            if ($parent instanceof Beneficiary) {
                return $parent;
            }
        }

        return null;
    }
}
