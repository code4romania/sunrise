<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Filament\Organizations\Resources\InterventionServiceResource;
use App\Services\Breadcrumb\InterventionPlanBreadcrumb;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditBeneficiaryIntervention extends EditRecord
{
    use HasParentResource;

    protected static string $resource = BeneficiaryInterventionResource::class;

    public function getBreadcrumbs(): array
    {
        return InterventionPlanBreadcrumb::make($this->getRecord()->interventionService->interventionPlan)
            ->getInterventionBreadcrumb($this->getRecord());
    }

    protected function getRedirectUrl(): ?string
    {
        return InterventionServiceResource::getUrl('view_intervention', [
            'parent' => $this->getRecord()->interventionService,
            'record' => $this->getRecord(),
        ]);
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->organizationServiceIntervention->serviceIntervention->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label(__('intervention_plan.actions.delete_beneficiary_intervention'))
                ->outlined()
                ->modalHeading(__('intervention_plan.headings.delete_beneficiary_intervention_modal'))
                ->successRedirectUrl(InterventionPlanResource::getUrl('view_intervention_service', [
                    'parent' => $this->getRecord()->interventionService,
                    'record' => $this->getRecord(),
                ])),
        ];
    }
}
