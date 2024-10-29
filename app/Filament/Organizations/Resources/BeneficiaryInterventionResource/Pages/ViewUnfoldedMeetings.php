<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages;

use App\Concerns\HasGroupPages;
use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Widgets\UnfoldedWidget;
use App\Services\Breadcrumb\InterventionPlanBreadcrumb;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewUnfoldedMeetings extends ViewRecord
{
    use HasParentResource;
    use HasGroupPages;

    protected static string $resource = BeneficiaryInterventionResource::class;

    protected static string $view = 'filament.organizations.pages.view-beneficiary-interventions';

    public function getBreadcrumbs(): array
    {
        return InterventionPlanBreadcrumb::make($this->getRecord()->interventionService->interventionPlan)
            ->getInterventionBreadcrumb($this->getRecord());
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->organizationServiceIntervention->serviceInterventionWithoutStatusCondition->name;
    }

    protected function getFooterWidgets(): array
    {
        return [
            UnfoldedWidget::class,
        ];
    }
}
